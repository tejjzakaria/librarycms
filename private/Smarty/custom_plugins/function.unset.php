<?php
    /**
     * Copyright (c) 2015 - 2018 by KAA Soft. All rights reserved.
     */

    /**
     * Smarty unset function
     * Type:     function<br>
     * Name:     unset<br>
     * Purpose:  Unset a given variable
     *
     * @author KAASoft <admin at kaasoft dot pro>
     * @author Alex Kasach
     * @param $params
     * @param $smarty Smarty
     */

    function smarty_function_unset($params, &$smarty) {
        $varName = $params["var"];

        $smarty->clearAssign($varName);
    }
