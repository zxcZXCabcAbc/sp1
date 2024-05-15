<?php
declare (strict_types = 1);

namespace app\controller\api;

use app\BaseController;
use think\Request;

class WebhookController extends BaseController
{

    public function index(Request $request)
    {
        $from = $request->param('from','paypal');
        tplog('webhook from ' . $from,$request->all());
        echo 'success';
    }


}
