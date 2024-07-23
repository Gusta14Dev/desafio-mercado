<?php

declare(strict_types=1);

namespace App\Config\Validator;

use DateTime;

class Validator
{

    private $errors = [];

    public function validate($data, $rules)
    {
        // if (empty($data)) {
        //     $this->errors['all'][] = 'No data provided for validation.';
        //     return false;
        // }

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) && in_array('required', $rule)) {
                $this->errors[$field][] = $this->getErrorMessage('required', $field);
                continue;
            }

            if (isset($rule)) {
                foreach ($rule as $ruleName) {
                    if (str_contains($ruleName, ':'))
                        list($ruleName, $parameter) = explode(":", $ruleName);

                    if (isset($parameter)) {
                        if (!$this->$ruleName($data[$field], $parameter)) {
                            $this->errors[$field][] = $this->getErrorMessage($ruleName, $field, $parameter);
                        }
                    }else{
                        if (!$this->$ruleName($data[$field])) {
                            $this->errors[$field][] = $this->getErrorMessage(rule: $ruleName, field: $field);
                        }
                    }
                }
            }
        }

        // foreach ($data as $field => $value) {
        //     if (!isset($data[$field])) {
        //         $this->errors[$field][] = $this->getErrorMessage('required', null, $field);
        //         continue;
        //     }

        //     if (isset($rules[$field])) {
        //         foreach ($rules[$field] as $rule => $parameter) {
        //             if (!$this->$rule($value, $parameter)) {
        //                 $this->errors[$field][] = $this->getErrorMessage($rule, $parameter, $field);
        //             }
        //         }
        //     }
        // }

        return empty($this->errors);
    }

    private function required($value)
    {
        return trim($value) != "";
    }

    private function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    private function integer($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function float($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    private function date($value, $format)
    {
        $date = DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) == $value;
    }

    private function getErrorMessage($rule, $field, $parameter = null)
    {
        switch ($rule) {
            case 'required':
                $message = "O campo '$field' é obrigatório.";
                break;
            case 'date':
                $message = "O campo '$field' tem que ser uma data no formato:";
                break;
            case 'email':
                $message = "O campo '$field' tem que ser um email válido.";
                break;
            case 'integer':
                $message = "O campo '$field' tem que ser um numero inteiro.";
                break;
            case 'float':
                $message = "O campo '$field' tem que ser um numero decimal.";
                break;
            default:
                $message = "O campo '$field' validação incorreta.";
                break;
        }

        if (!is_null($parameter))
            $message .= " ($parameter)";

        return $message;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
