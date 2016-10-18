from __future__ import print_function
import sys, os
import re
import wustl_python.player


def read_data(data):
    players = dict()

    f = open(data)
    for line in f:
        # print ("Read line: %s" % line.rstrip())
        theplayer = wustl_python.player.Player('player')
        try:
            # Taylor Douthit batted 3 times with 1 hits and 0 runs
            reresult = re.search('^(.+) batted (\d) times .+(\d) hits.+', line.rstrip())
            theplayer.name = reresult.group(1)
            theplayer.bat = int(reresult.group(2))
            theplayer.hit = int(reresult.group(3))
            # print (theplayer.name, theplayer.bat, theplayer.hit)
            if theplayer.name in players:
                players[theplayer.name].bat += theplayer.bat
                players[theplayer.name].hit += theplayer.hit
            else:
                players[theplayer.name] = theplayer
        except AttributeError:
            pass

    f.close()
    return players


def main():
    # arguments handling
    if len(sys.argv) < 2:
        sys.exit("Usage: %s filename" % sys.argv[0])
    filename = sys.argv[1]
    if not os.path.exists(filename):
        sys.exit("Error: File '%s' not found" % sys.argv[1])

    result = read_data(filename)
    sresult = reversed(sorted(result, key=lambda name: result[name].get_batavg()))
    for player in sresult:
        print(player, round(result[player].get_batavg(), 3))


if __name__ == "__main__":
    main()
