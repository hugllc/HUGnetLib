describe("HUGnet.Histories", function() {

    describe("When it is initialized", function() {
        var histories;

        beforeEach(function() {
            histories = new HUGnet.Histories();
        });
        afterEach(function() {
        });

        it("it should be empty", function() {
            expect(histories.length).toEqual(0);
        });

    });
    describe("When adding and deleting records", function() {
        var histories;

        beforeEach(function() {
            histories = new HUGnet.Histories();
        });
        afterEach(function() {
        });

        it("it should keep them in order by date", function() {
            histories.add([
                { id: 3, Date: 1, Data0: 0, Data1: 1 },
                { id: 3, Date: 5, Data0: 0, Data1: 1 },
                { id: 3, Date: 2, Data0: 0, Data1: 1 },
                { id: 3, Date: 4, Data0: 0, Data1: 1 },
                { id: 3, Date: 3, Data0: 0, Data1: 1 },
            ]);
            expect(histories.pluck("Date")).toEqual([1, 2, 3, 4, 5]);
        });
        it("it should not add the same record more than once", function() {
            histories.add([
                { id: 3, Date: 1, Data0: 1, Data1: 1 },
                { id: 3, Date: 1, Data0: 2, Data1: 1 },
                { id: 3, Date: 1, Data0: 3, Data1: 1 },
                { id: 3, Date: 1, Data0: 4, Data1: 1 },
                { id: 3, Date: 2, Data0: 5, Data1: 1 },
            ]);
            expect(histories.pluck("Date")).toEqual([1, 2]);
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