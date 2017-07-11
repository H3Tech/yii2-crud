CREATE TABLE `crud_media` (
  `id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `crud_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);
ALTER TABLE `crud_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;