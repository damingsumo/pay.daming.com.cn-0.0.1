<?php
class Controller_Ali_Prompt extends Controller_Base {
    
    public function payShow() {
        return $this->display('wxpayment/pay', array());
    }
}
?>