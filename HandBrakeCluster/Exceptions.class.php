<?php

class HandBrakeCluster_Exception extends Exception {};

class HandBrakeCluster_Exception_DatabaseConfigMissing extends Exception {};
class HandBrakeCluster_Exception_DatabaseConnectFailed extends Exception {};
class HandBrakeCluster_Exception_NoDatabaseConnection  extends Exception {};
class HandBrakeCluster_Exception_DatabaseQueryFailed   extends Exception {};
class HandBrakeCluster_Exception_ResultCountMismatch   extends Exception {};

class HandBrakeCluster_Exception_UnknownSetting        extends Exception {};

?>
