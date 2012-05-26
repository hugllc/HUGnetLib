describe("src/webapi/javascript/models/HUGnet.Device", function() {

    describe("when it is initialized", function() {
        var device;

        beforeEach(function() {
            var device = new HUGnet.Device({
            });
        });

        it("DeviceID should be set to the id", function() {
            var dev = new HUGnet.Device({
                id: 0x123456
            });
            expect(dev.get('DeviceID')).toEqual('123456');
        });
        it("but only if DeviceID is not already set", function() {
            var dev = new HUGnet.Device({
                id: 0x123456,
                DeviceID: '654321'
            });
            expect(dev.get('DeviceID')).toEqual('654321');
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