<?php
 namespace MailPoetVendor\Doctrine\ORM\Cache; if (!defined('ABSPATH')) exit; use MailPoetVendor\Doctrine\Common\Collections\Collection; use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata; use MailPoetVendor\Doctrine\ORM\PersistentCollection; interface CollectionHydrator { public function buildCacheEntry(\MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata $metadata, \MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey $key, $collection); public function loadCacheEntry(\MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata $metadata, \MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheKey $key, \MailPoetVendor\Doctrine\ORM\Cache\CollectionCacheEntry $entry, \MailPoetVendor\Doctrine\ORM\PersistentCollection $collection); } 