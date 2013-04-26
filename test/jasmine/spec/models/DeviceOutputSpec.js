describe("src/webapi/javascript/models/HUGnet.DeviceOutput", function() {

    describe("when it is initialized", function() {
        var output;

        beforeEach(function() {
            output = new HUGnet.DeviceOutput({
                id: 0,
                dev: 0x12,
            });
        });

        it("driver should default to SDEFAULT", function() {
            expect(output.get('driver')).toEqual('SDEFAULT');
        });
        it("type should default to Unknown", function() {
            expect(output.get('type')).toEqual('Unknown');
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
