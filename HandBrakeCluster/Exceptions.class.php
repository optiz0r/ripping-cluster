<?php

class HandBrakeCluster_Exception extends Exception {};

class HandBrakeCluster_Exception_DatabaseException      extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_DatabaseConfigMissing  extends HandBrakeCluster_Exception_DatabaseException {};
class HandBrakeCluster_Exception_DatabaseConnectFailed  extends HandBrakeCluster_Exception_DatabaseException {};
class HandBrakeCluster_Exception_NoDatabaseConnection   extends HandBrakeCluster_Exception_DatabaseException {};
class HandBrakeCluster_Exception_DatabaseQueryFailed    extends HandBrakeCluster_Exception_DatabaseException {};
class HandBrakeCluster_Exception_ResultCountMismatch    extends HandBrakeCluster_Exception_DatabaseException {};

class HandBrakeCluster_Exception_ConfigException        extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_UnknownSetting         extends HandBrakeCluster_Exception_ConfigException {};

class HandBrakeCluster_Exception_TemplateException      extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_Unauthorized           extends HandBrakeCluster_Exception_TemplateException {};
class HandBrakeCluster_Exception_FileNotFound           extends HandBrakeCluster_Exception_TemplateException {};
class HandBrakeCluster_Exception_InvalidParameters      extends HandBrakeCluster_Exception_TemplateException {};

class HandBrakeCluster_Exception_InvalidSourceDirectory extends HandBrakeCluster_Exception {};

class HandBrakeCluster_Exception_CacheException         extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_InvalidCacheDir        extends HandBrakeCluster_Exception_CacheException {};
class HandBrakeCluster_Exception_CacheObjectNotFound    extends HandBrakeCluster_Exception_CacheException {};

?>