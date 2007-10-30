<?php
// Call e00392800Test::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "00392800Test::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../endpointTest.php';

/**
 * Test class for endpoints.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:06:08.
 */
class e00392800Test extends endpointTestBase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("e00392800Test");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

}

// Call e00392800Test::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "e00392800Test::main") {
    e00392800Test::main();
}
?>
