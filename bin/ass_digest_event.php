<?php

$_POST['type'] = 'dig';
$_POST['timestamp'] = date( 'Y-m-d H:i:s' );

class HC_BPGES_Async_Request_Send_Queue extends BPGES_Async_Request_Send_Queue {
	function handle() {
		return parent::handle();
	}
}

$q = new HC_BPGES_Async_Request_Send_Queue();
$q->handle();
