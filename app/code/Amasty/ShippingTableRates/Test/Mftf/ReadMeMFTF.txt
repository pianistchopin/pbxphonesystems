ReadeMeMFTF (It is recommendations for runs tests).

    Quantity all tests equals 30.

        For runs test (qty 24), use group "ShippingTableRates".
            command: "vendor/bin/mftf run:group ShippingTableRates -r"


        Some tests (qty 6) have preconditions, so they runs with suite. They check Shipping Type.
            For correct operation you need to run the test group "STRCheckShippingTypeSuite" for this 6 tests.
            command: "vendor/bin/mftf run:group STRCheckShippingTypeSuite -r"
