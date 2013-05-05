describe("src/webapi/javascript/models/HUGnet.DeviceProcess", function() {

    describe("when it is initialized", function() {
        var process;

        beforeEach(function() {
            process = new HUGnet.DeviceProcess({
                id: 0,
                dev: 0x12,
            });
        });

        it("driver should default to SDEFAULT", function() {
            expect(process.get('driver')).toEqual('SDEFAULT');
        });
        it("type should default to Unknown", function() {
            expect(process.get('type')).toEqual('Unknown');
        });
        it("extraDesc should default to an empty array", function() {
            expect(process.get('extraDesc')).toEqual({});
        });
        it("extraDefault should default to an empty array", function() {
            expect(process.get('extraDefault')).toEqual({});
        });
        it("extraText should default to an empty array", function() {
            expect(process.get('extraText')).toEqual({});
        });
        it("extraValues should default to an empty array", function() {
            expect(process.get('extraValues')).toEqual({});
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
