describe("src/webapi/javascript/models/HUGnet.DeviceFunction", function() {

    describe("when it is initialized", function() {
        var input;

        beforeEach(function() {
            input = new HUGnet.DeviceFunction({
                id: 0,
                dev: 0x12,
            });
        });

        it("driver should default to empty", function() {
            expect(input.get('driver')).toEqual('');
        });
        it("tableEntry should default to an empty array", function() {
            expect(input.get('data')).toEqual({});
        });
        it("longName should default to 'Unknown'", function() {
            expect(input.get('longName')).toEqual('Unknown');
        });
        it("shortName should default to 'Unknown'", function() {
            expect(input.get('shortName')).toEqual('Unknown');
        });
        it("name should default to 'New Function'", function() {
            expect(input.get('name')).toEqual('New Function');
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
