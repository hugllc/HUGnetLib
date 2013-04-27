describe("src/webapi/javascript/models/HUGnet.InputTables", function() {

    describe("When it is initialized", function() {
        var inputtables;

        beforeEach(function() {
            inputtables = new HUGnet.InputTables();
        });
        afterEach(function() {
        });

        it("it should be empty", function() {
            expect(inputtables.length).toEqual(0);
        });

    });
    describe("When adding and deleting records", function() {
        var inputtables;

        beforeEach(function() {
            inputtables = new HUGnet.InputTables();
        });
        afterEach(function() {
        });

        it("it should keep them in order by id", function() {
            inputtables.add([
                { id: 1, name: "a", arch: 0, desc: 1 },
                { id: 5, name: "b", arch: 0, desc: 1 },
                { id: 2, name: "c", arch: 0, desc: 1 },
                { id: 4, name: "d", arch: 0, desc: 1 },
                { id: 3, name: "e", arch: 0, desc: 1 },
            ]);
            expect(inputtables.pluck("id")).toEqual([1, 2, 3, 4, 5]);
        });
        it("it should not add the same record more than once", function() {
            inputtables.add([
                { id: 1, name: 1, arch: 1, desc: 1 },
                { id: 1, name: 1, arch: 2, desc: 1 },
                { id: 1, name: 1, arch: 3, desc: 1 },
                { id: 1, name: 1, arch: 4, desc: 1 },
                { id: 2, name: 2, arch: 5, desc: 1 },
            ]);
            expect(inputtables.pluck("id")).toEqual([1, 2]);
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
