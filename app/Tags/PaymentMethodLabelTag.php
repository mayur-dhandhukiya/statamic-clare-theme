<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Globals\GlobalSet;

class PaymentMethodLabelTag extends Tags
{
    protected static $handle = 'payment_method_label';

    public function label()
    {
        $globals = GlobalSet::findByHandle('payment_setting')->inDefaultSite();
        $methods = $globals->get('payment_methods');
        $method = $this->params->get('methodkey');
        
        return $methods[$method] ?? '-';
    }
}
