var lineup_size = 9;
var num_innings = 9;
var outs, inning, battingTeam, pitchingTeam, inGame;
var inningChart = [[], []];
var playerStats = [[], []];
var scores = [];
var pitchers =[0, 0];
var bases = [-1, -1, -1];
var currentBatters = [];
var team_names = [];
var rosters = [[], []];
var lineups = [[], []];
var ids_toName = [[], []];
var restings = [[], []];
var playersInGame = [[], []]; //Players who have already played
var positions = [[], []];
var categories = [];
var abbreviations = [];
var lastBatter = {}; //team, id
var lastPitcher = {}; //team, id
var lastInningPitcher = {}; //team, id
var winningPitcher = {}; //team, id
var losingPitcher = {}; //team, id
var startingPitchers = [{}, {}]; //id
var lastPlayStatus;
//var actions = ["Fly Out", "Strikeout", "Single", "Double", "Triple", "Home Run", "Walk", "Error", "Pop/Foul/Line Out", "LoMax", "Ground Out", "Caught Stealing", "Stolen Base", "Pinch Hitter", "Pinch Runner", "Change Pitcher","Manual Change"];
var ACTIONS_TO_MESSAGE = {
    "Fly Out" : "flew out.",
    "Strikeout" : "struck out.",
    "Pop/Foul/Line Out" : "got out.",
    "LoMax" : "hit a LoMax.",
    "Ground Out" : "grounded out.",
    "Error" : "got on base due to an error!",
    "Walk" : "was walked!",
    "SB_1" : "stole second base!",
    "SB_2" : "stole thrid base!",
    "SB_3" : "stole home!",
    "SBD_1" : "stole second base!",
    "SBD_2" : "stole third base!",
    "SBD_3" : "stole home!",
    "CS_1" : "was caught stealing second base.",
    "CS_2" : "was caught stealing third base.",
    "CS_3" : "was caught stealing home.",
    "OB_1" : "witnessed a random out at first base.",
    "OB_2" : "witnessed a random out at second base.",
    "OB_3" : "witnessed a random out at third base.",
    "RB_1" : "witnessed a random disappearance at first base.",
    "RB_2" : "witnessed a random disappearance at second base.",
    "RB_3" : "witnessed a random disappearance at third base.",
    "Single" : "hit a single!",
    "Double" : "hit a double!",
    "Triple" : "hit a triple!",
    "Home Run" : "hit a home run!",
};