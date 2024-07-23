--
-- Database: `desafio_mercado`
--
CREATE DATABASE IF NOT EXISTS `desafio_mercado` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `desafio_mercado`;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `type_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_sale`
--

CREATE TABLE `product_sale` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `sale_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `price_unity` float NOT NULL,
  `price_total` float NOT NULL,
  `tax_total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `date` datetime NOT NULL,
  `price_total` float NOT NULL,
  `price_total_taxes` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `type_products`
--

CREATE TABLE `type_products` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `porcent_tax` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD KEY `type_index` (`type_id`) USING BTREE;

--
-- Indexes for table `product_sale`
--
ALTER TABLE `product_sale`
  ADD KEY `sales_index` (`sale_id`) USING BTREE,
  ADD KEY `products_index` (`product_id`) USING BTREE;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `type_products` (`id`);

--
-- Constraints for table `product_sale`
--
ALTER TABLE `product_sale`
  ADD CONSTRAINT `product_sale_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `product_sale_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;
