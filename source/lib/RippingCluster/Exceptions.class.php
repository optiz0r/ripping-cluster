<?php

class RippingCluster_Exception extends Exception {};

class RippingCluster_Exception_InvalidSourceDirectory extends RippingCluster_Exception {};

class RippingCluster_Exception_LogicException         extends RippingCluster_Exception {};
class RippingCluster_Exception_JobNotRunning          extends RippingCluster_Exception_LogicException {};


?>
