from __future__ import division


class Player:
    # constructor:
    def __init__(self, name):
        self.name = name
        self.bat = 0.0
        self.hit = 0.0
        self.run = 0.0

    def get_batavg(self):
        return self.hit / self.bat


def main():
    player = Player('player')
    player.bat = 100
    player.hit = 7
    print (player.get_batavg())


if __name__ == "__main__":
    main()
