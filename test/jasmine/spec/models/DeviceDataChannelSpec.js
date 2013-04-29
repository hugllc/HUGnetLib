describe("src/webapi/javascript/models/HUGnet.DeviceDataChannel", function() {

    describe("when it is initialized", function() {
        var datachan;

        beforeEach(function() {
            datachan = new HUGnet.DeviceDataChannel({
                id: 0,
                dev: 0x12,
            });
        });

        it("dataType should default to Ignore", function() {
            expect(datachan.get('dataType')).toEqual('ignore');
        });
        it("type should default to Unknown", function() {
            expect(datachan.get('type')).toEqual('Unknown');
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
