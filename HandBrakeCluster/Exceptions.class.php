<?php

class HandBrakeCluster_Exception extends Exception {};

class HandBrakeCluster_Exception_DatabaseConfigMissing extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_DatabaseConnectFailed extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_NoDatabaseConnection  extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_DatabaseQueryFailed   extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_ResultCountMismatch   extends HandBrakeCluster_Exception {};

class HandBrakeCluster_Exception_UnknownSetting        extends HandBrakeCluster_Exception {};

class HandBrakeCluster_Exception_TemplateException     extends HandBrakeCluster_Exception {};
class HandBrakeCluster_Exception_Unauthorized          extends HandBrakeCluster_Exception_TemplateException {};
class HandBrakeCluster_Exception_FileNotFound          extends HandBrakeCluster_Exception_TemplateException {};

?>
