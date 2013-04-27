describe("src/webapi/javascript/models/HUGnet.ProcessTable", function() {

    describe("when it is initialized", function() {
        var processtable;

        beforeEach(function() {
            processtable = new HUGnet.ProcessTable({
                id: 12,
                dev: 0x12,
            });
        });

        it("id should be set to 12", function() {
            expect(processtable.get('id')).toEqual(12);
        });
        it("params should be an array", function() {
            expect(processtable.get('params')).toEqual({});
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
