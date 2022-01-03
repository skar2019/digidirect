<?php
 
namespace Digidirect\ChangeOrderStatus\Plugin;
 
class HistoryPlugin
{   
    public function afterGetStatus(\Magento\Sales\Model\Order\Status\History $subject, $result)
    {            
        if ($result == 'Pending') {
            return 'Processing';
        }
        
        return $result;
    }    
}
?>