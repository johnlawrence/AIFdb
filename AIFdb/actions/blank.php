<?php

class BlankAction extends Action {
    function handle($args) {
        parent::handle($args);
        common_template('clean', '', 'AIF2DB');
    }
}
