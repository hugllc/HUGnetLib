describe("src/webapi/javascript/models/HUGnet.Annotation", function() {

    describe("when it is initialized", function() {
        var annotation;

        beforeEach(function() {
            var annotation = new HUGnet.Annotation({
            });
        });

        it("ID should be set to the id", function() {
            var dev = new HUGnet.Annotation({
                id: 41
            });
            expect(dev.get('id')).toEqual(41);
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
