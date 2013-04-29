describe("src/webapi/javascript/models/HUGnet.DeviceDataChannel", function() {

    describe("when it is initialized", function() {
        var datachan;

        beforeEach(function() {
            datachan = new HUGnet.DeviceDataChannel({
                channel: 1,
                dev: 0x12,
            });
        });

        it("channel should be set to 1", function() {
            expect(datachan.get('channel')).toEqual(1);
        });
        it("dev should be set to 0x12", function() {
            expect(datachan.get('dev')).toEqual(0x12);
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
