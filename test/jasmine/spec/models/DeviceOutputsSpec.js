describe("src/webapi/javascript/models/HUGnet.DeviceOutputs", function() {

    describe("When it is initialized", function() {
        var outputs;

        beforeEach(function() {
            outputs = new HUGnet.DeviceOutputs();
        });
        afterEach(function() {
        });

        it("it should be empty", function() {
            expect(outputs.length).toEqual(0);
        });

    });
    describe("When adding and deleting records", function() {
        var outputs;

        beforeEach(function() {
            outputs = new HUGnet.DeviceOutputs();
        });
        afterEach(function() {
        });

        it("it should keep them in order by output", function() {
            outputs.add([
                { output: 1, driver: "a", dev: 0, type: 1 },
                { output: 5, driver: "b", dev: 0, type: 1 },
                { output: 2, driver: "c", dev: 0, type: 1 },
                { output: 4, driver: "d", dev: 0, type: 1 },
                { output: 3, driver: "e", dev: 0, type: 1 },
            ]);
            expect(outputs.pluck("output")).toEqual([1, 2, 3, 4, 5]);
        });
        it("it should not add the same record more than once", function() {
            outputs.add([
                { output: 1, driver: 1, dev: 1, type: 1 },
                { output: 1, driver: 1, dev: 2, type: 1 },
                { output: 1, driver: 1, dev: 3, type: 1 },
                { output: 1, driver: 1, dev: 4, type: 1 },
                { output: 2, driver: 2, dev: 5, type: 1 },
            ]);
            expect(outputs.pluck("output")).toEqual([1, 2]);
        });

    });
/*
  // demonstrates use of spies to intercept and test method calls
  it("tells the current song if the user has made it a favorite", function() {
    spyOn(song, 'persistFavoriteStatus');

    player.play(song);
    player.makeFavorite();

    expect(song.persistFavoriteStatus).toHaveBeenCalledWith(true);
  });

  //demonstrates use of expected exceptions
  describe("#resume", function() {
    it("should throw an exception if song is already playing", function() {
      player.play(song);

      expect(function() {
        player.resume();
      }).toThrow("song is already playing");
    });
  });
*/
});
