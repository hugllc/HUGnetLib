describe("src/webapi/javascript/views/HUGnet.viewHelpers", function() {

    describe("when selectInt is called", function() {
        it("it should return options tags for a select field", function() {
            expect(HUGnet.viewHelpers.selectInt(0, 2, 1))
                .toEqual('<option value="0">0</option><option value="1">1</option><option value="2">2</option>');
        });

        it("it should correctly increment the values", function() {
            expect(HUGnet.viewHelpers.selectInt(0, 2, 2))
                .toEqual('<option value="0">0</option><option value="2">2</option>');
        });
        it("it should select the correct value", function() {
            expect(HUGnet.viewHelpers.selectInt(0, 2, 1, 1))
                .toEqual('<option value="0">0</option><option value="1" selected="selected">1</option><option value="2">2</option>');
        });
        it("it should return nothing if start > end and increment is positive", function() {
            expect(HUGnet.viewHelpers.selectInt(2, 0, 1, -1))
                .toEqual('');
        });
        it("it should return a backwards select if start > end and increment is negative", function() {
            expect(HUGnet.viewHelpers.selectInt(2, 0, -1, 1))
                .toEqual('<option value="2">2</option><option value="1" selected="selected">1</option><option value="0">0</option>');
        });
        it("it should take negative numbers", function() {
            expect(HUGnet.viewHelpers.selectInt(0, -2, -1, -1))
                .toEqual('<option value="0">0</option><option value="-1" selected="selected">-1</option><option value="-2">-2</option>');
        });
        it("it should return nothing if inc === 0", function() {
            expect(HUGnet.viewHelpers.selectInt(0, 2, 0, 1))
                .toEqual('');
        });
    });
    describe("when formatDate is called", function() {
        it("it should return the date as a string", function() {
            var date = 123456789;
            var d = new Date(date * 1000);
            expect(HUGnet.viewHelpers.formatDate(date))
                .toEqual(d.toString());
        });
        it("it should return 'Never' as the default alternate when date is undefined", function() {
            var date;
            expect(HUGnet.viewHelpers.formatDate(date))
                .toEqual("Never");
        });
        it("it should return 'Never' as the default alternate when date is 0", function() {
            var date;
            expect(HUGnet.viewHelpers.formatDate(0))
                .toEqual("Never");
        });
        it("it should return the given alternate if date is 0", function() {
            var date;
            expect(HUGnet.viewHelpers.formatDate(0, "Not Here"))
                .toEqual("Not Here");
        });
    });
});
