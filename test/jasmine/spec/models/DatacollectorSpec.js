describe("src/webapi/javascript/models/HUGnet.Datacollector", function() {

    describe("when it is initialized", function() {
        var datacollector;

        beforeEach(function() {
            var datacollector = new HUGnet.Datacollector({
            });
        });

        it("UUID should be set to the uuid", function() {
            var dev = new HUGnet.Datacollector({
                uuid: '01a2d127-116e-4e3d-ad42-4ee65feb9e7b'
            });
            expect(dev.get('uuid')).toEqual('01a2d127-116e-4e3d-ad42-4ee65feb9e7b');
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
