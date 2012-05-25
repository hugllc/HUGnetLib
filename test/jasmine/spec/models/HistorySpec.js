describe("HUGnet.History", function() {

    describe("when it is initialized", function() {
        var history;

        beforeEach(function() {
            history = new HUGnet.History({
                id: 1,
                Date: 123456789,
            });
        });

        it("UnixDate should be set to (1000 * Date)", function() {
            expect(history.get('UnixDate')).toEqual(history.get('Date') * 1000);
        });
        it("Type should default to history", function() {
            expect(history.get('Type')).toEqual('history');
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