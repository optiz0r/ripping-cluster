<?php

class RippingCluster_Exception extends Exception {};

class RippingCluster_Exception_DatabaseException      extends RippingCluster_Exception {};
class RippingCluster_Exception_DatabaseConfigMissing  extends RippingCluster_Exception_DatabaseException {};
class RippingCluster_Exception_DatabaseConnectFailed  extends RippingCluster_Exception_DatabaseException {};
class RippingCluster_Exception_NoDatabaseConnection   extends RippingCluster_Exception_DatabaseException {};
class RippingCluster_Exception_DatabaseQueryFailed    extends RippingCluster_Exception_DatabaseException {};
class RippingCluster_Exception_ResultCountMismatch    extends RippingCluster_Exception_DatabaseException {};

class RippingCluster_Exception_ConfigException        extends RippingCluster_Exception {};
class RippingCluster_Exception_UnknownSetting         extends RippingCluster_Exception_ConfigException {};

class RippingCluster_Exception_TemplateException      extends RippingCluster_Exception {};
class RippingCluster_Exception_AbortEntirePage        extends RippingCluster_Exception_TemplateException {}; 
class RippingCluster_Exception_Unauthorized           extends RippingCluster_Exception_TemplateException {};
class RippingCluster_Exception_FileNotFound           extends RippingCluster_Exception_TemplateException {};
class RippingCluster_Exception_InvalidParameters      extends RippingCluster_Exception_TemplateException {};

class RippingCluster_Exception_InvalidSourceDirectory extends RippingCluster_Exception {};

class RippingCluster_Exception_CacheException         extends RippingCluster_Exception {};
class RippingCluster_Exception_InvalidCacheDir        extends RippingCluster_Exception_CacheException {};
class RippingCluster_Exception_CacheObjectNotFound    extends RippingCluster_Exception_CacheException {};

class RippingCluster_Exception_LogicException         extends RippingCluster_Exception {};
class RippingCluster_Exception_JobNotRunning          extends RippingCluster_Exception_LogicException {};

class RippingCluster_Exception_InvalidPluginName      extends RippingCluster_Exception {};

?>
