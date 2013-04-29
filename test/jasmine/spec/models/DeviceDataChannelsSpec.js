describe("src/webapi/javascript/models/HUGnet.DeviceDataChannels", function() {

    describe("When it is initialized", function() {
        var datachans;

        beforeEach(function() {
            datachans = new HUGnet.DeviceDataChannels();
        });
        afterEach(function() {
        });

        it("it should be empty", function() {
            expect(datachans.length).toEqual(0);
        });

    });
    describe("When adding and deleting records", function() {
        var datachans;

        beforeEach(function() {
            datachans = new HUGnet.DeviceDataChannels();
        });
        afterEach(function() {
        });

        it("it should keep them in order by channel", function() {
            datachans.add([
                { channel: 1, label: "a", dev: 0, type: 1 },
                { channel: 5, label: "b", dev: 0, type: 1 },
                { channel: 2, label: "c", dev: 0, type: 1 },
                { channel: 4, label: "d", dev: 0, type: 1 },
                { channel: 3, label: "e", dev: 0, type: 1 },
            ]);
            expect(datachans.pluck("channel")).toEqual([1, 2, 3, 4, 5]);
        });
        it("it should not add the same record more than once", function() {
            datachans.add([
                { channel: 1, label: 1, dev: 1, type: 1 },
                { channel: 1, label: 1, dev: 2, type: 1 },
                { channel: 1, label: 1, dev: 3, type: 1 },
                { channel: 1, label: 1, dev: 4, type: 1 },
                { channel: 2, label: 2, dev: 5, type: 1 },
            ]);
            expect(datachans.pluck("channel")).toEqual([1, 2]);
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
