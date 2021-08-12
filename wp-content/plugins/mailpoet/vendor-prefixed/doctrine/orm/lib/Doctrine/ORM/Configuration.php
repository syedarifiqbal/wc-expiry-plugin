<?php
 namespace MailPoetVendor\Doctrine\ORM; if (!defined('ABSPATH')) exit; use MailPoetVendor\Doctrine\Common\Annotations\AnnotationReader; use MailPoetVendor\Doctrine\Common\Annotations\AnnotationRegistry; use MailPoetVendor\Doctrine\Common\Annotations\CachedReader; use MailPoetVendor\Doctrine\Common\Annotations\SimpleAnnotationReader; use MailPoetVendor\Doctrine\Common\Cache\ArrayCache; use MailPoetVendor\Doctrine\Common\Cache\Cache as CacheDriver; use MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter; use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider; use MailPoetVendor\Doctrine\Common\Proxy\AbstractProxyFactory; use MailPoetVendor\Doctrine\Deprecations\Deprecation; use MailPoetVendor\Doctrine\ORM\Cache\CacheConfiguration; use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataFactory; use MailPoetVendor\Doctrine\ORM\Mapping\DefaultEntityListenerResolver; use MailPoetVendor\Doctrine\ORM\Mapping\DefaultNamingStrategy; use MailPoetVendor\Doctrine\ORM\Mapping\DefaultQuoteStrategy; use MailPoetVendor\Doctrine\ORM\Mapping\Driver\AnnotationDriver; use MailPoetVendor\Doctrine\ORM\Mapping\EntityListenerResolver; use MailPoetVendor\Doctrine\ORM\Mapping\NamingStrategy; use MailPoetVendor\Doctrine\ORM\Mapping\QuoteStrategy; use MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping; use MailPoetVendor\Doctrine\ORM\Repository\DefaultRepositoryFactory; use MailPoetVendor\Doctrine\ORM\Repository\RepositoryFactory; use MailPoetVendor\Doctrine\Persistence\Mapping\Driver\MappingDriver; use MailPoetVendor\Doctrine\Persistence\ObjectRepository; use MailPoetVendor\Psr\Cache\CacheItemPoolInterface; use ReflectionClass; use function class_exists; use function strtolower; use function trim; class Configuration extends \MailPoetVendor\Doctrine\DBAL\Configuration { public function setProxyDir($dir) { $this->_attributes['proxyDir'] = $dir; } public function getProxyDir() { return $this->_attributes['proxyDir'] ?? null; } public function getAutoGenerateProxyClasses() { return $this->_attributes['autoGenerateProxyClasses'] ?? \MailPoetVendor\Doctrine\Common\Proxy\AbstractProxyFactory::AUTOGENERATE_ALWAYS; } public function setAutoGenerateProxyClasses($autoGenerate) { $this->_attributes['autoGenerateProxyClasses'] = (int) $autoGenerate; } public function getProxyNamespace() { return $this->_attributes['proxyNamespace'] ?? null; } public function setProxyNamespace($ns) { $this->_attributes['proxyNamespace'] = $ns; } public function setMetadataDriverImpl(\MailPoetVendor\Doctrine\Persistence\Mapping\Driver\MappingDriver $driverImpl) { $this->_attributes['metadataDriverImpl'] = $driverImpl; } public function newDefaultAnnotationDriver($paths = [], $useSimpleAnnotationReader = \true) { \MailPoetVendor\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . '/Mapping/Driver/DoctrineAnnotations.php'); if ($useSimpleAnnotationReader) { $reader = new \MailPoetVendor\Doctrine\Common\Annotations\SimpleAnnotationReader(); $reader->addNamespace('MailPoetVendor\\Doctrine\\ORM\\Mapping'); } else { $reader = new \MailPoetVendor\Doctrine\Common\Annotations\AnnotationReader(); } if (\class_exists(\MailPoetVendor\Doctrine\Common\Cache\ArrayCache::class)) { $reader = new \MailPoetVendor\Doctrine\Common\Annotations\CachedReader($reader, new \MailPoetVendor\Doctrine\Common\Cache\ArrayCache()); } return new \MailPoetVendor\Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, (array) $paths); } public function addEntityNamespace($alias, $namespace) { $this->_attributes['entityNamespaces'][$alias] = $namespace; } public function getEntityNamespace($entityNamespaceAlias) { if (!isset($this->_attributes['entityNamespaces'][$entityNamespaceAlias])) { throw \MailPoetVendor\Doctrine\ORM\ORMException::unknownEntityNamespace($entityNamespaceAlias); } return \trim($this->_attributes['entityNamespaces'][$entityNamespaceAlias], '\\'); } public function setEntityNamespaces(array $entityNamespaces) { $this->_attributes['entityNamespaces'] = $entityNamespaces; } public function getEntityNamespaces() { return $this->_attributes['entityNamespaces']; } public function getMetadataDriverImpl() { return $this->_attributes['metadataDriverImpl'] ?? null; } public function getQueryCacheImpl() { return $this->_attributes['queryCacheImpl'] ?? null; } public function setQueryCacheImpl(\MailPoetVendor\Doctrine\Common\Cache\Cache $cacheImpl) { $this->_attributes['queryCacheImpl'] = $cacheImpl; } public function getHydrationCacheImpl() { return $this->_attributes['hydrationCacheImpl'] ?? null; } public function setHydrationCacheImpl(\MailPoetVendor\Doctrine\Common\Cache\Cache $cacheImpl) { $this->_attributes['hydrationCacheImpl'] = $cacheImpl; } public function getMetadataCacheImpl() { \MailPoetVendor\Doctrine\Deprecations\Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8650', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use getMetadataCache() instead.', __METHOD__); if (isset($this->_attributes['metadataCacheImpl'])) { return $this->_attributes['metadataCacheImpl']; } return isset($this->_attributes['metadataCache']) ? \MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider::wrap($this->_attributes['metadataCache']) : null; } public function setMetadataCacheImpl(\MailPoetVendor\Doctrine\Common\Cache\Cache $cacheImpl) { \MailPoetVendor\Doctrine\Deprecations\Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/8650', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use setMetadataCache() instead.', __METHOD__); $this->_attributes['metadataCacheImpl'] = $cacheImpl; $this->_attributes['metadataCache'] = \MailPoetVendor\Doctrine\Common\Cache\Psr6\CacheAdapter::wrap($cacheImpl); } public function getMetadataCache() : ?\MailPoetVendor\Psr\Cache\CacheItemPoolInterface { return $this->_attributes['metadataCache'] ?? null; } public function setMetadataCache(\MailPoetVendor\Psr\Cache\CacheItemPoolInterface $cache) : void { $this->_attributes['metadataCache'] = $cache; $this->_attributes['metadataCacheImpl'] = \MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider::wrap($cache); } public function addNamedQuery($name, $dql) { $this->_attributes['namedQueries'][$name] = $dql; } public function getNamedQuery($name) { if (!isset($this->_attributes['namedQueries'][$name])) { throw \MailPoetVendor\Doctrine\ORM\ORMException::namedQueryNotFound($name); } return $this->_attributes['namedQueries'][$name]; } public function addNamedNativeQuery($name, $sql, \MailPoetVendor\Doctrine\ORM\Query\ResultSetMapping $rsm) { $this->_attributes['namedNativeQueries'][$name] = [$sql, $rsm]; } public function getNamedNativeQuery($name) { if (!isset($this->_attributes['namedNativeQueries'][$name])) { throw \MailPoetVendor\Doctrine\ORM\ORMException::namedNativeQueryNotFound($name); } return $this->_attributes['namedNativeQueries'][$name]; } public function ensureProductionSettings() { $queryCacheImpl = $this->getQueryCacheImpl(); if (!$queryCacheImpl) { throw \MailPoetVendor\Doctrine\ORM\ORMException::queryCacheNotConfigured(); } if ($queryCacheImpl instanceof \MailPoetVendor\Doctrine\Common\Cache\ArrayCache) { throw \MailPoetVendor\Doctrine\ORM\ORMException::queryCacheUsesNonPersistentCache($queryCacheImpl); } if ($this->getAutoGenerateProxyClasses()) { throw \MailPoetVendor\Doctrine\ORM\ORMException::proxyClassesAlwaysRegenerating(); } if (!$this->getMetadataCache()) { throw \MailPoetVendor\Doctrine\ORM\ORMException::metadataCacheNotConfigured(); } $metadataCacheImpl = $this->getMetadataCacheImpl(); if ($metadataCacheImpl instanceof \MailPoetVendor\Doctrine\Common\Cache\ArrayCache) { throw \MailPoetVendor\Doctrine\ORM\ORMException::metadataCacheUsesNonPersistentCache($metadataCacheImpl); } } public function addCustomStringFunction($name, $className) { $this->_attributes['customStringFunctions'][\strtolower($name)] = $className; } public function getCustomStringFunction($name) { $name = \strtolower($name); return $this->_attributes['customStringFunctions'][$name] ?? null; } public function setCustomStringFunctions(array $functions) { foreach ($functions as $name => $className) { $this->addCustomStringFunction($name, $className); } } public function addCustomNumericFunction($name, $className) { $this->_attributes['customNumericFunctions'][\strtolower($name)] = $className; } public function getCustomNumericFunction($name) { $name = \strtolower($name); return $this->_attributes['customNumericFunctions'][$name] ?? null; } public function setCustomNumericFunctions(array $functions) { foreach ($functions as $name => $className) { $this->addCustomNumericFunction($name, $className); } } public function addCustomDatetimeFunction($name, $className) { $this->_attributes['customDatetimeFunctions'][\strtolower($name)] = $className; } public function getCustomDatetimeFunction($name) { $name = \strtolower($name); return $this->_attributes['customDatetimeFunctions'][$name] ?? null; } public function setCustomDatetimeFunctions(array $functions) { foreach ($functions as $name => $className) { $this->addCustomDatetimeFunction($name, $className); } } public function setCustomHydrationModes($modes) { $this->_attributes['customHydrationModes'] = []; foreach ($modes as $modeName => $hydrator) { $this->addCustomHydrationMode($modeName, $hydrator); } } public function getCustomHydrationMode($modeName) { return $this->_attributes['customHydrationModes'][$modeName] ?? null; } public function addCustomHydrationMode($modeName, $hydrator) { $this->_attributes['customHydrationModes'][$modeName] = $hydrator; } public function setClassMetadataFactoryName($cmfName) { $this->_attributes['classMetadataFactoryName'] = $cmfName; } public function getClassMetadataFactoryName() { if (!isset($this->_attributes['classMetadataFactoryName'])) { $this->_attributes['classMetadataFactoryName'] = \MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadataFactory::class; } return $this->_attributes['classMetadataFactoryName']; } public function addFilter($name, $className) { $this->_attributes['filters'][$name] = $className; } public function getFilterClassName($name) { return $this->_attributes['filters'][$name] ?? null; } public function setDefaultRepositoryClassName($className) { $reflectionClass = new \ReflectionClass($className); if (!$reflectionClass->implementsInterface(\MailPoetVendor\Doctrine\Persistence\ObjectRepository::class)) { throw \MailPoetVendor\Doctrine\ORM\ORMException::invalidEntityRepository($className); } $this->_attributes['defaultRepositoryClassName'] = $className; } public function getDefaultRepositoryClassName() { return $this->_attributes['defaultRepositoryClassName'] ?? \MailPoetVendor\Doctrine\ORM\EntityRepository::class; } public function setNamingStrategy(\MailPoetVendor\Doctrine\ORM\Mapping\NamingStrategy $namingStrategy) { $this->_attributes['namingStrategy'] = $namingStrategy; } public function getNamingStrategy() { if (!isset($this->_attributes['namingStrategy'])) { $this->_attributes['namingStrategy'] = new \MailPoetVendor\Doctrine\ORM\Mapping\DefaultNamingStrategy(); } return $this->_attributes['namingStrategy']; } public function setQuoteStrategy(\MailPoetVendor\Doctrine\ORM\Mapping\QuoteStrategy $quoteStrategy) { $this->_attributes['quoteStrategy'] = $quoteStrategy; } public function getQuoteStrategy() { if (!isset($this->_attributes['quoteStrategy'])) { $this->_attributes['quoteStrategy'] = new \MailPoetVendor\Doctrine\ORM\Mapping\DefaultQuoteStrategy(); } return $this->_attributes['quoteStrategy']; } public function setEntityListenerResolver(\MailPoetVendor\Doctrine\ORM\Mapping\EntityListenerResolver $resolver) { $this->_attributes['entityListenerResolver'] = $resolver; } public function getEntityListenerResolver() { if (!isset($this->_attributes['entityListenerResolver'])) { $this->_attributes['entityListenerResolver'] = new \MailPoetVendor\Doctrine\ORM\Mapping\DefaultEntityListenerResolver(); } return $this->_attributes['entityListenerResolver']; } public function setRepositoryFactory(\MailPoetVendor\Doctrine\ORM\Repository\RepositoryFactory $repositoryFactory) { $this->_attributes['repositoryFactory'] = $repositoryFactory; } public function getRepositoryFactory() { return $this->_attributes['repositoryFactory'] ?? new \MailPoetVendor\Doctrine\ORM\Repository\DefaultRepositoryFactory(); } public function isSecondLevelCacheEnabled() { return $this->_attributes['isSecondLevelCacheEnabled'] ?? \false; } public function setSecondLevelCacheEnabled($flag = \true) { $this->_attributes['isSecondLevelCacheEnabled'] = (bool) $flag; } public function setSecondLevelCacheConfiguration(\MailPoetVendor\Doctrine\ORM\Cache\CacheConfiguration $cacheConfig) { $this->_attributes['secondLevelCacheConfiguration'] = $cacheConfig; } public function getSecondLevelCacheConfiguration() { if (!isset($this->_attributes['secondLevelCacheConfiguration']) && $this->isSecondLevelCacheEnabled()) { $this->_attributes['secondLevelCacheConfiguration'] = new \MailPoetVendor\Doctrine\ORM\Cache\CacheConfiguration(); } return $this->_attributes['secondLevelCacheConfiguration'] ?? null; } public function getDefaultQueryHints() { return $this->_attributes['defaultQueryHints'] ?? []; } public function setDefaultQueryHints(array $defaultQueryHints) { $this->_attributes['defaultQueryHints'] = $defaultQueryHints; } public function getDefaultQueryHint($name) { return $this->_attributes['defaultQueryHints'][$name] ?? \false; } public function setDefaultQueryHint($name, $value) { $this->_attributes['defaultQueryHints'][$name] = $value; } } 