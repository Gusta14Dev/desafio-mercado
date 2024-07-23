<?php

declare(strict_types=1);

namespace App\Config\DB;

use PDO;

class DB
{
  private static ?DB $instance = null;
  private static PDO $db;

  private function __construct()
  {
    // Conectar ao banco de dados usando as configurações fornecidas

    $dbConfig = [
      'connection' => env('DB_CONNECTION'),
      'host' => env('DB_HOST'),
      'dbname' => env('DB_DATABASE'),
      'port' => env('DB_PORT'),
      'username' => env('DB_USERNAME'),
      'password' => env('DB_PASSWORD')
    ];

    self::$db = new PDO(
      "{$dbConfig['connection']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset=utf8",
      $dbConfig['username'],
      $dbConfig['password']
    );

    // Definir atributos de erro para PDO
    self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  }

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public static function create(mixed $model, array $data = []): int
  {
    $columns = implode(', ', array_keys($data));
    $values = implode(', ', array_fill(0, count($data), '?'));

    $sql = "INSERT INTO " . $model::getTableName() . " ($columns) VALUES ($values)";
    $stmt = self::$db->prepare($sql);

    $stmt->execute(array_values($data));
    return intval(self::$db->lastInsertId());
  }

  public static function select(mixed $model, null|array $where = null, null|array $orderBy = null, null|int $limit = null): array
  {
    $sql = "SELECT * FROM " . $model::getTableName();

    if ($where) {
      $conditions = [];
      foreach ($where as $column => $value) {
        $conditions[] = "$column = ?";
      }

      $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    if ($orderBy) {
      $orders = [];
      foreach ($orderBy as $column => $direction) {
        $orders[] = "$column $direction";
      }

      $sql .= " ORDER BY " . implode(', ', $orders);
    }

    if ($limit) {
      $sql .= " LIMIT {$limit}";
    }

    $stmt = self::$db->prepare($sql);

    if ($where) {
      $i = 1;
      foreach ($where as $value) {
        $stmt->bindParam($i, $value);
        $i++;
      }
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $objects = [];
    foreach ($results as $data) {
      $object = new $model($data);
      if (!empty($object->getRelationships()))
        $object->loadRelationships($data);

      if ($limit > 1 || is_null($limit))
        $objects[] = $object->extractData();
      else
        $objects = $object->extractData();
    }

    return $objects;
  }

  public static function update(mixed $model, array $data = [], null|array $where = null): int
  {
    $set_columns = [];
    foreach ($data as $key => $value) {
      $set_columns[] = "$key = ?";
    }

    $sql = "UPDATE " . $model::getTableName() . " SET " . implode(', ', $set_columns);

    if ($where) {
      $conditions = [];
      foreach ($where as $column => $value) {
        $conditions[] = "$column = ?";
      }

      $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $binds = array_merge($data, $where ?? []);

    $stmt = self::$db->prepare($sql);

    $i = 1;
    foreach ($binds as $value) {
      $stmt->bindValue($i, $value);
      $i++;
    }

    $stmt->execute();
    return $stmt->rowCount();
  }

  public static function delete(mixed $model, null|array $where = null): bool
  {
    $sql = "DELETE FROM " . $model::getTableName();

    if ($where) {
      $conditions = [];
      foreach ($where as $column => $value) {
        $conditions[] = "$column = ?";
      }

      $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $stmt = self::$db->prepare($sql);

    if ($where) {
      $i = 1;
      foreach ($where as $value) {
        $stmt->bindParam($i, $value);
        $i++;
      }
    }
    return $stmt->execute();
  }

  public static function findWithRelationships(mixed $model, int $id): mixed
  {
    $sql = "SELECT * FROM " . $model::getTableName() . " WHERE id = ?";
    $stmt = self::$db->prepare($sql);
    $stmt->bindParam(1, $id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data) {
      return null;
    }

    $modelObject = new $model($data);
    $modelObject->loadRelationships($data);
    return $modelObject;
  }

  public static function saveWithRelationships(mixed $model, array $data): int
  {
    $relationships = $model->getRelationships();

    try {
      self::$db->beginTransaction();
        $id = self::create($model, $data);
        foreach ($relationships as $relationshipName => $relationshipClass) {
          $relationshipData = $model->$relationshipName->extractData();
          $relationshipData[$model::getRelation() . '_id'] = $id;

          self::create($relationshipClass, $relationshipData);
        }
      self::$db->commit();
      return $id;
    } catch (Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }

  public static function deleteWithRelationships(mixed $model, int $id): bool
  {
    try {
      self::$db->beginTransaction();
      $relationships = $model::getRelationships();
      foreach ($relationships as $relationshipName => $relationshipClass) {
        self::delete($relationshipClass, [$model::getRelation() . '_id' => $id]);
      }
      self::delete($model, ['id' => $id]);
      self::$db->commit();
      return true;
    } catch (Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }
}
