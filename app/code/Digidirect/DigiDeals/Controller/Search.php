<?php

namespace Digidirect\Digideals\Controller;

class Search {

    public function execute() {

        if (isset($_POST["innerSearchInput"])) {
            return $_POST["innerSearchInput"];
        } else {
            return 'No data!';
        }
    }

}
