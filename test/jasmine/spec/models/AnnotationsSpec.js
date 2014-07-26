describe("src/webapi/javascript/models/HUGnet.Annotations", function() {

    describe("When it is initialized", function() {
        var annotations;

        beforeEach(function() {
            annotations = new HUGnet.Annotations();
        });
        afterEach(function() {
        });

        it("it should be empty", function() {
            expect(annotations.length).toEqual(0);
        });

    });
    describe("When adding and deleting records", function() {
        var annotations;

        beforeEach(function() {
            annotations = new HUGnet.Annotations();
        });
        afterEach(function() {
        });

        it("it should keep them in order by id", function() {
            annotations.add([
                { id: 1, DeviceID: 1, HWPartNum: 0, FWPartNum: 1 },
                { id: 5, DeviceID: 5, HWPartNum: 0, FWPartNum: 1 },
                { id: 2, DeviceID: 2, HWPartNum: 0, FWPartNum: 1 },
                { id: 4, DeviceID: 4, HWPartNum: 0, FWPartNum: 1 },
                { id: 3, DeviceID: 3, HWPartNum: 0, FWPartNum: 1 },
            ]);
            expect(annotations.pluck("id")).toEqual([1, 2, 3, 4, 5]);
        });
        it("it should not add the same record more than once", function() {
            annotations.add([
                { id: 3, DeviceID: 1, HWPartNum: 1, FWPartNum: 1 },
                { id: 3, DeviceID: 1, HWPartNum: 2, FWPartNum: 1 },
                { id: 3, DeviceID: 1, HWPartNum: 3, FWPartNum: 1 },
                { id: 3, DeviceID: 1, HWPartNum: 4, FWPartNum: 1 },
                { id: 4, DeviceID: 2, HWPartNum: 5, FWPartNum: 1 },
            ]);
            expect(annotations.pluck("DeviceID")).toEqual([1, 2]);
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
