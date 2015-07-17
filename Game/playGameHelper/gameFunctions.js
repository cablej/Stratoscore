//Updates the lineup onscreen. removePlayer=true if x is wanted to be displayed
function refreshLineup(team, removePlayer) {
	var lineup = "#team" + (team + 1) + "_lineup";
	$(lineup).empty();
	var header = "<tr><th>Number</th><th>Name</th>";
	if (removePlayer) {
		header += "<th>X</th>";
	}
	header += "</tr>";
	$(lineup).append(header);
	for (var i = 0; i < lineups[team].length; i++) {
		str = "<tr><td>" + (i + 1) + "</td><td>" + ids_toName[team][lineups[team][i]] + "</td>";
		if (removePlayer) {
			str += "<td><a style='cursor:pointer' onclick='removeFromLineup(" + team + "," + lineups[team][i] + ")'><u>X</u></a></td>";
		}
		str += "</tr>";
		$(lineup).append(str);
	}
	//pitch = "#team"+(team+1)+"_pitcher";
	var pitcher_name = ids_toName[team][pitchers[team]];
	if (pitcher_name) {
		str = "<tr><td>P</td><td>" + pitcher_name + "</td>";
		if (removePlayer) {
			str += "<td><a style='cursor:pointer' onclick='pitchers[" + team + "] = 0; refreshLineup(" + team + ", true)'><u>X</u></a></td>";
		}
		str += "</tr>";
		$(lineup).append(str);
		//$(pitch).text("Pitcher: " + pitcher_name);
	} else {
		$(lineup).append("<tr><td>P</td><td>No Pitcher</td></tr>")
		//$(pitch).text("No pitcher.");
	}
}
//Updates the roster. onclick=none gives no link for name(to add to roster)
function refreshRoster(team, onclick) {
	team_label = "team" + (team + 1);
	table = "#" + team_label + "_table";
	$(table).empty();
	$(table).append("<tr><th>Name</th><th>Position</th><th>Player Card</th><th>ID</th></tr>");
	team_name = team_names[team];
	if (onclick == 'none') {
		for (var i = 0; i < rosters[team].length; i++) {
			id = rosters[team][i];
			full_name = ids_toName[team][id];
			position = positions[team][id];
			$(table).append("<tr><td>" + full_name + "</td><td>" + position.substr(0,2) + "</td><td><p><a style='cursor:pointer' target='_blank' href='../Standings/Team/Player/displayPlayer.php?team="+team_name+"&id="+id+"'><u>[View]</u></a></p></td><td>" + id + "</td></tr>");
		}
	} else if (onclick == 'error') {
		for (var i = 0; i < rosters[team].length; i++) {
			id = rosters[team][i];
			full_name = ids_toName[team][id];
			position = positions[team][id];
			$(table).append("<tr><td onclick='error(" + id + "," + team + ")'>" + full_name + "</td><td>" + position.substr(0,2) + "</td><td><p><a target='_blank' href='displayPlayer.php?id=" + id + "&team=" + team_name + "'>[View]</a></p></td><td>" + id + "</td></tr>");
		}
	}
}

//Adds a player to the lineup
function addToLineup(team, id) {
	if (inGame) {
		$("#message").text("The game has already started.");
		return false;
	}
	if(restings[team][id] != "0") {
		$("#message").text(ids_toName[team][id] + " has to rest for " + restings[team][id] + " games.");
		return false;
	}
	if (lineups[team].length == lineup_size) {
		if ((positions[team][id].indexOf("P") != - 1 || positions[team][id].indexOf("CL") != - 1)) {
			pitchers[team] = id;
			refreshLineup(team, true);
			$("#message").text("Successfully changed the " + team_names[team] + " pitcher to " + ids_toName[team][id] + ".");
			checkDisabled();
		}
		else {
			$("#message").text("There are already " + lineup_size + " players in the " + team_names[team] + " lineup.");
		}
		return false;
	}
	for (var i = 0; i < lineups[team].length; i++) {
		if (lineups[team][i] == id) {
			$("#message").text("Sorry but " + ids_toName[team][id] + " is already in the " + team_names[team] + " lineup.");
			return false;
		}
	}
	lineups[team].push(id);
	$("#message").text("Successfully added " + ids_toName[team][id] + " to the " + team_names[team] + " lineup.");
	refreshLineup(team, true);
	checkDisabled();
	return true;
}

//Removes a player from the lineup
function removeFromLineup(team, id) {
	if (inGame) {
		$("#message").text("The game has already started.");
		return false;
	}
	for (var i = 0; i < lineups[team].length; i++) {
		if (lineups[team][i] == id) {
			lineups[team].remove(i);
			refreshLineup(team, true);
			$("#message").text("Successfully removed " + ids_toName[team][id] + " from the " + team_names[team] + " lineup.");
			checkDisabled();
			return true;
		}
	}
}

//Checks to see if the button should be disabled to progress the game
function checkDisabled() {
	if (lineups[0].length == lineup_size && lineups[1].length == lineup_size && pitchers[0] !== 0 && pitchers[1] !== 0) {
		$("#finalize").removeAttr("disabled");
	} else {
		$("#finalize").attr("disabled", "disabled");
	}
}

//Finalizes the lineups to begin the game
function finalizeLineups() {
	if (lineups[0].length == lineup_size && lineups[1].length == lineup_size) {
		inGame = true;
		refreshLineup(0, false);
		refreshLineup(1, false);
		refreshRoster(0, 'none');
		refreshRoster(1, 'none');
		for(var i=0; i<lineups.length; i++) {
			playersInGame[i].push(pitchers[i]);
			for(var j=0; j<lineups[i].length; j++) {
				if(pitchers[i] != lineups[i][j])
					playersInGame[i].push(lineups[i][j]);
			}
		}
		startingPitchers[0] = pitchers[0];
		lastInningPitcher.id = pitchers[1];
		lastInningPitcher.team = 1;
		startingPitchers[1] = pitchers[1];
		$("#finalize").hide();
		$("#message").text("The game has started!");
		startGame();
	} else {
		$("#message").text("Your team does not have nine players.");
	}
}

//Updates the scoreboard
function refreshScoreboard() {
	if (inGame) {
		$("#score").text(team_names[0] + ": " + scores[0] + " " + team_names[1] + ": " + scores[1]);
		$("#outs").text("Outs: " + outs);
		if (battingTeam === 0) {
			$("#inning").text("It is the bottom of inning " + inning + ".");
		} else if (battingTeam == 1) {
			$("#inning").text("It is the top of inning " + inning + ".");
		}
		text = "";
		if (bases[0] != -1) {
			text += "First Base: " + ids_toName[battingTeam][bases[0]] + " ";
		} else {
			text += "First Base: None ";
		}
		if (bases[1] != -1) {
			text += "Second Base: " + ids_toName[battingTeam][bases[1]] + " ";
		} else {
			text += "Second Base: None ";
		}
		if (bases[2] != -1) {
			text += "Third Base: " + ids_toName[battingTeam][bases[2]] + " ";
		} else {
			text += "Third Base: None ";
		}
		$("#bases").text(text);
		$("#current").text(ids_toName[battingTeam][lineups[battingTeam][currentBatters[battingTeam]]] + " is batting against " + ids_toName[pitchingTeam][pitchers[pitchingTeam]] + ".");
	}
}

//Initializes the game
function startGame() {
	$("#scoreboard").show();
	$("#actionsDiv").show();
	$("#wait").hide();
	outs = 0;
	inning = 1;
	battingTeam = 1;
	pitchingTeam = 0;
	scores[0] = 0;
	scores[1] = 0;
	currentBatters[0] = 0;
	currentBatters[1] = 0;
	inningChart[0][inning - 1] = 0;
	inningChart[1][inning - 1] = 0;
	refreshScoreboard();
}

//Advances the out, and, if necesary, advances the inning
function advanceOut() {
	outs++;
	if (outs == 3) {
		advanceInning();
		return true;
	}
	return false;
}

function advanceInning() {
	if (inning >= num_innings) {
		if (battingTeam == 1 && scores[0] > scores[1]) {
			finishGame();
			return;
		} else if (battingTeam == 0 && scores[0] != scores[1]) {
			finishGame();
			return;
		}
	}
	lastInningPitcher.id = pitchers[pitchingTeam];
	lastInningPitcher.team = pitchingTeam;
	if (battingTeam == 0) {
		inning++;
		inningChart[0][inning - 1] = 0;
		inningChart[1][inning - 1] = 0;
	}
	bases = [-1, -1, -1];
	temp = battingTeam;
	battingTeam = pitchingTeam;
	pitchingTeam = temp;
	outs = 0;
}

function advanceBatter() {
	batter = currentBatters[battingTeam];
	if (batter == lineup_size - 1) {
		currentBatters[battingTeam] = 0;
	} else {
		currentBatters[battingTeam]++;
	}
}

function increaseScore(team) {
	scores[team]++;
	inningChart[team][inning - 1]++;
	
	var otherTeam = team === 0 ? 1 : 0;
	
	if(scores[team] > scores[otherTeam]) {
		winningPitcher.id = lastInningPitcher.id;
		winningPitcher.team = lastInningPitcher.team;
		
		losingPitcher.id = pitchers[pitchingTeam];
		losingPitcher.team = pitchingTeam;
	}
}

function numOccurences(arr, str) {
	var count = 0;
	for (var z = 0; z<arr.length; z++) {
	   if(arr[z] == str) count++;
	}
	return count;
}

function finishGame() {
	if(winningPitcher.id == startingPitchers[winningPitcher.team]) {
		var inns = true;
		for(n=0; n<playerStats.length; n++) {
			if(playerStats[n][0].id == winningPitcher.id) {
				if(numOccurences(playerStats[n][0].stats, "Outs Pitched") < 15) {
					//Did not pitch 5 innings
					inns = false;
					break;
				}
			}
		}
		
		if(!inns) {
			var found = false;
			for(n=0; n<playerStats.length; n++) {
				if(playerStats[n][0].team == winningPitcher.team && numOccurences(playerStats[n][0].stats, "Outs Pitched") >= 15) {
					//Did pitch 5 innings
					winningPitcher.id = playerStats[n][0].id;
					found = true;
					break;
				}
			}
			if(!found) {
				winningPitcher.id = "";
			}
		}
	}
	
	if(winningPitcher.id != "") {
		playerw = {};
		playerw.id = winningPitcher.id;
		playerw.stats = [];
		playerw.stats.push("Wins");
		playerw.team = winningPitcher.team;
		appendStats(playerw);
	}
	playerl = {};
	playerl.id = losingPitcher.id;
	playerl.stats = [];
	playerl.stats.push("Losses");
	playerl.team = losingPitcher.team;
	appendStats(playerl);
	
	xml_update = "<statupdate>";
	for (x = 0; x < playerStats.length; x++) {
		for (y = 0; y < playerStats[x].length; y++) {
			player = playerStats[x][y];
			stats = makeStats(player.stats);
			xml_stats = generatePlayerStats(player.id, player.team, stats);
			xml_update += xml_stats;
		}
	}
	xml_update += "</statupdate>";
	submitStats(xml_update);
	submitEndGame();
	inGame = false;
	refreshScoreboard();
	setTimeout(function() {
		location.reload();
	}, 500);
}

function makeStats(names) {
	stats = [new Array(categories[0].length), new Array(categories[1].length)];
	for (var i = 0; i < stats.length; i++) {
		for (j = 0; j < stats[i].length; j++) {
			stats[i][j] = 0;
			for (h = 0; h < names.length; h++) {
				if (categories[i][j] == names[h]) {
					stats[i][j]++;
				}
			}
		}
	}
	return stats;
}

function generatePlayerStats(id, team, stats) {
	stats_xml = "<player id='" + id + "' team='" + team + "'><stats>";
	for (var i = 0; i < stats.length; i++) {
		stats_xml += "<category>";
		for (j = 0; j < stats[i].length; j++) {
			stats_xml += "<stat>";
			stats_xml += stats[i][j];
			stats_xml += "</stat>";
		}
		stats_xml += "</category>";
	}
	stats_xml += "</stats></player>"
	return stats_xml;
}

function inLineup(team, id) {
	for (var i = 0; i < lineups[team].length; i++) {
		if (id == lineups[team][i]) {
			return true;
		}
	}
	return false;
}

function inRoster(team, id) {
	for (var i = 0; i < rosters[team].length; i++) {
		if (id == rosters[team][i]) {
			return true;
		}
	}
	return false;
}

function outsAdd(num) {
	if(outs + num > 2) {
		advanceInning();
	} else if(outs + num < 0) {
		outs = 0;
	}
	else {
		outs += num;
	}
	refreshScoreboard();
}

function pinchRun(base) {
	if(bases[base-1] == -1) return;
	
	var id;
	while (true) {
		id = prompt("What is the id of the new runner?");
		if(id === "") {
			return;
		}
		if(!inRoster(battingTeam, id)) {
			alert("Player must be in the roster of the batting team. Give no id to exit.");
		}
		else if(restings[battingTeam][id] != "0") {
			alert(ids_toName[team][id] + " has to rest for " + restings[team][id] + " games. Give no id to exit");
		}
		else if(contains(playersInGame[battingTeam], id)) {
			alert("Player has already played. Give no id to exit.");
		} else break;
	}
	if(bases[base-1] == pitchers[battingTeam]) {
		pitchers[battingTeam] = -1;
	}
	bases[num-1] = id;
	playersInGame[battingTeam].push(id);
	$("#message").text("Successfully changed base " + num + " to " + ids_toName[battingTeam][id] + ".");
	refreshScoreboard();
}

function pinchHit() {
	var id;
	while (true) {
		id = prompt("What is the id of the new batter?");
		if(id === "") {
			return;
		}
		if(!inRoster(battingTeam, id)) {
			alert("Player must be in the roster of the batting team. Give no id to exit.");
		}
		else if(restings[battingTeam][id] != "0") {
			alert(ids_toName[team][id] + " has to rest for " + restings[team][id] + " games. Give no id to exit");
		}
		else if(contains(playersInGame[battingTeam], id)) {
			alert("Player has already played. Give no id to exit.");
		} else break;
	}
	if(lineups[battingTeam][currentBatters[battingTeam]] == pitchers[battingTeam]) {
		pitchers[battingTeam] = -1;
	}
	lineups[battingTeam][currentBatters[battingTeam]] = id;
	playersInGame[battingTeam].push(id);
	$("#message").text("Successfully changed batter to " + ids_toName[battingTeam][id] + ".");
	refreshScoreboard();
	refreshLineup(battingTeam, false);
}

function changePitcher() {
	var id;
	while (true) {
		id = prompt("What is the id of the new player?");
		if(id === "") {
			return;
		}
		if(!inRoster(pitchingTeam, id)) {
			alert("Player must be in the roster of the batting team. Give no id to exit.");
		}
		else if(restings[pitchingTeam][id] != "0") {
			alert(ids_toName[pitchingTeam][id] + " has to rest for " + restings[pitchingTeam][id] + " games. Give no id to exit");
		}
		else if(contains(playersInGame[pitchingTeam], id)) {
			alert("Player has already played. Give no id to exit.");
		} else if(positions[pitchingTeam][id].indexOf("P") == - 1 && positions[pitchingTeam][id].indexOf("CL") == - 1) /*is not pitcher*/{
			alert("Player is not a pitcher or has to rest. Give no id to exit.");
		}
		else break;
	}
	pitchers[pitchingTeam] = id;
	playersInGame[pitchingTeam].push(id);
	$("#message").text("Successfully changed pitcher to " + ids_toName[pitchingTeam][id] + ".");
	refreshScoreboard();
	refreshLineup(pitchingTeam, false);
}

function processAction(action) {
	if(pitchers[pitchingTeam] == -1) {
		alert("You must select a pitcher.");
		return;
	}
	/*lastPlayStatus.playerStats = playerStats;
	lastPlayStatus.outs = outs;
	lastPlayStatus.scores = scores;
	lastPlayStatus.inning = inning;
	lastPlayStatus.battingTeam = battingTeam;*/
	/*lastPlayStatus.pitchingTeam = pitchingTeam;
	lastPlayStatus.bases = bases;
	lastPlayStatus.currentBatters = currentBatters;
	lastPlayStatus.inningChart = inningChart;
	lastPlayStatus.lineups = lineups;
	lastPlayStatus.pitchers = pitchers;*/
	batter_id = lineups[battingTeam][currentBatters[battingTeam]];
	pitcher_id = pitchers[pitchingTeam];
	batter_name = ids_toName[battingTeam][batter_id];
	pitcher_name = ids_toName[pitchingTeam][pitcher_id];
	message = "<p>" + batter_name + " " + ACTIONS_TO_MESSAGE[action];
	stats = [];
	stats[0] = {};
	stats[0].id = batter_id;
	stats[0].team = battingTeam;
	stats[0].stats = [];
	stats[1] = {};
	stats[1].id = pitcher_id;
	stats[1].team = pitchingTeam;
	stats[1].stats = [];
	//New Batter
	if (contains(["Fly Out", "Strikeout", "Single", "Double", "Triple", "Home Run", "Pop/Foul/Line Out", "LoMax", "Ground Out", "Error", "Walk"], action)) {
		advanceBatter();
		lastBatter.team = battingTeam;
		lastBatter.id = batter_id;
		lastPitcher.team = pitchingTeam;
		lastPitcher.id = pitcher_id;
	}
	//At Bats
	if (contains(["Fly Out", "Strikeout", "Single", "Double", "Triple", "Home Run", "Pop/Foul/Line Out", "LoMax", "Ground Out", "Error"], action)) {
		stats[0].stats.push("At Bats");
	}
	//Pitcher Outs
	if (contains(["Fly Out", "Strikeout", "Pop/Foul/Line Out", "Ground Out", "Caught Stealing"], action)) {
		stats[1].stats.push("Outs Pitched");
	}
	//Walk
	if (action == "Walk") {
		stats[0].stats.push("BBs");
		stats[1].stats.push("Walks");
		if (bases[0] != -1 && bases[1] != -1 && bases[2] != -1) {
			player = {};
			player.id = bases[2];
			player.stats = [];
			player.stats.push("Runs");
			player.team = battingTeam;
			stats.push(player);
			scoring_name = ids_toName[battingTeam][bases[2]];
			message += " " + scoring_name + " scored on the play.";
			bases[2] = -1;
			increaseScore(battingTeam);
			stats[0].stats.push("RBIs");
			stats[1].stats.push("Earned Runs");
			stats[1].stats.push("Runs Allowed");
		}
		if (bases[0] != -1 && bases[1] != -1) {
			bases[2] = bases[1];
		}
		if (bases[0] != -1) {
			bases[1] = bases[0];
		}
		bases[0] = batter_id;
	}
	//LoMax
	if (action == "LoMax") {
		outsGenerated = 1;
		names = [];
		for (var i = 0; i < bases.length; i++) {
			if (bases[i] != -1) {
				names.push(ids_toName[battingTeam][bases[i]]);
				outsGenerated++;
			}
		}
		message += " ";
		for (var i = 0; i < names.length; i++) {
			message += names[i];
			if (i != names.length - 1) {
				message += ", ";
			}
			if (i == names.length - 2 && names.length > 1) {
				message += "and ";
			}
		}
		message += " also got out on the play.";
		bases = [-1, -1, -1];
		for (var i = 0; i < outsGenerated; i++) {
			advanced = advanceOut();
			stats[1].stats.push("Outs Pitched");
			if (advanced) {
				break;
			}
		}
	}
	//Strikeout
	if (action == "Strikeout") {
		stats[0].stats.push("Strikeouts");
		stats[1].stats.push("Ks");
	}
	//Double
	if (action == "Double") {
		stats[0].stats.push("Doubles");
	}
	//Triple
	if (action == "Triple") {
		stats[0].stats.push("Triples");
	}
	//Home Run
	if (action == "Home Run") {
		stats[0].stats.push("Home Runs");
		stats[0].stats.push("RBIs");
		stats[0].stats.push("Runs");
		stats[1].stats.push("Earned Runs");
		stats[1].stats.push("Runs Allowed");
		stats[1].stats.push("Home Runs Allowed");
	}
	//Error, must come before hits because of terminator on return.
	if (action == "Error") {
		while (true) {
			id = prompt("What is the id of the player who made the error?");
			if (id === "") {
				return;
			}
			if (inRoster(pitchingTeam, id)) {
				break;
			}
		}
		player_error = {};
		player_error.id = id;
		player_error.team = team;
		player_error.stats = [];
		player_error.stats.push("Errors");
		stats.push(player_error);
	}
	//Stolen Base
	if (action.substring(0, 3) == "SB_") {
		type = action.charAt(action.length - 1);
		for (var i = type; i > 0; i--) {
			if (i == 3) {
				if (bases[2] == -1) break;
				player = {};
				player.id = bases[2];
				player.team = battingTeam;
				player.stats = [];
				player.stats.push("Runs");
				player.stats.push("Stolen Bases");
				stats.push(player);
				stats[1].stats.push("Earned Runs");
				stats[1].stats.push("Runs Allowed");
				increaseScore(battingTeam);
				bases[2] = -1;
			} else if (i == 2) {
				if (bases[1] == -1 || bases[2] != -1) break;
				player = {};
				player.id = bases[1];
				player.team = battingTeam;
				player.stats = [];
				player.stats.push("Stolen Bases");
				stats.push(player);
				bases[2] = bases[1];
				bases[1] = -1;
			} else if (i == 1) {
				if (bases[0] == -1 || bases[1] != -1) break;
				player = {};
				player.id = bases[0];
				player.team = battingTeam;
				player.stats = [];
				player.stats.push("Stolen Bases");
				stats.push(player);
				bases[1] = bases[0];
				bases[0] = -1;
			}
		}
	}
	//Stolen Base, don't advance all other runners
	if (action.substring(0, 3) == "SBD") {
		type = action.charAt(action.length - 1);
		if (type == 3) {
			if (bases[2] != -1) {
				player = {};
				player.id = bases[2];
				player.team = battingTeam;
				player.stats = [];
				player.stats.push("Runs");
				player.stats.push("Stolen Bases");
				stats.push(player);
				stats[1].stats.push("Earned Runs");
				stats[1].stats.push("Runs Allowed");
				increaseScore(battingTeam);
				bases[2] = -1;
			}
		} else if (type == 2) {
			if (!(bases[1] == -1 || bases[2] != -1)) {
				bases[2] = bases[1];
				bases[1] = -1;
			}
		} else if (type == 1) {
			if (!(bases[0] == -1 || bases[1] != -1)) {
				bases[1] = bases[0];
				bases[0] = -1;
			}
		}
	}
	//Caught Stealing
	if (action.substring(0, 3) == "CS_") {
		type = action.charAt(action.length - 1);
		player = {};
		player.id = bases[type - 1];
		player.team = battingTeam;
		player.stats = [];
		player.stats.push("Caught Stealing");
		stats.push(player);
		stats[1].stats.push("Outs Pitched");
		bases[type - 1] = -1;
		for (var i = type - 2; i >= 0; i--) {
			bases[i + 1] = bases[i];
			bases[i] = -1;
		}
	}
	//Advance at base, with stats
	if (action.substring(0, 3) == "AS_") {
		type = action.charAt(action.length - 1);
		if(bases[type - 1] != -1 && lastBatter.id) {
			if(type == 3) {
				player = {};
				player.id = bases[2];
				player.stats = [];
				player.stats.push("Runs");
				player.team = battingTeam;
				stats.push(player);
				
				playerb = {};
				playerb.id = lastBatter.id;
				playerb.stats = [];
				playerb.stats.push("RBIs");
				playerb.team = lastBatter.team;
				stats.push(playerb);
				
				playerp = {};
				playerp.id = lastPitcher.id;
				playerp.stats = [];
				playerp.stats.push("Runs Allowed");
				playerp.stats.push("Earned Runs");
				playerp.team = lastPitcher.team;
				stats.push(playerp);
				
				bases[2] = -1;
				
				increaseScore(battingTeam);
			} else if(bases[type] == -1) {
				bases[type] = bases[type-1];
				bases[type-1] = -1;
			}
		}
	}
	//Advance at base, no stats
	if (action.substring(0, 3) == "AN_") {
		type = action.charAt(action.length - 1);
		if(bases[type - 1] != -1) {
			if(type == 3) {
				bases[2] = -1;
			} else if(bases[type] == -1) {
				bases[type] = bases[type-1];
				bases[type-1] = -1;
			}
		}
	}
	//Out at base
	if (action.substring(0, 3) == "OB_") {
		type = action.charAt(action.length - 1);
		if(bases[type - 1] != -1) {
			stats[1].stats.push("Outs Pitched");
			bases[type - 1] = -1;
			advanceOut();
		}
	}
	//Remove at base
	if (action.substring(0, 3) == "RB_") {
		type = action.charAt(action.length - 1);
		if(bases[type - 1] != -1) {
			bases[type - 1] = -1;
		}
	}
	//All types of hits
	if (contains(["Single", "Double", "Triple", "Home Run", "Error"], action)) {
		stats[0].stats.push("Hits");
		stats[1].stats.push("Hits Allowed");
		if (action == "Single" || action == "Error") num = 1;
		else if (action == "Double") num = 2;
		else if (action == "Triple") num = 3;
		else if (action == "Home Run") num = 3;
		for (var i = 0; i < num; i++) {
			for (var j = bases.length - 1; j >= 0; j--) {
				if (bases[j] != -1) {
					if (j == bases.length - 1) {
						player = {};
						player.id = bases[j];
						player.stats = [];
						player.stats.push("Runs");
						player.team = battingTeam;
						stats.push(player);
						bases[j] = -1;
						increaseScore(battingTeam);
						if (action != "Error") {
							stats[0].stats.push("RBIs");
							stats[1].stats.push("Earned Runs");
						}
						stats[1].stats.push("Runs Allowed");
					} else {
						bases[j + 1] = bases[j];
						bases[j] = -1;
					}
				}
			}
		}
		if (action != "Home Run") {
			bases[num - 1] = batter_id;
		} else {
			increaseScore(battingTeam);
		}
	}
	lightCopyOfStats = stats.slice(0);
	for (var f = 0; f < lightCopyOfStats.length; f++) {
		appendStats(lightCopyOfStats[f]);
	}
	//Advances the out at the end, if necessary. Does it at the end because otherwise the game could end before all the stats are in.
	if (contains(["Fly Out", "Strikeout", "Pop/Foul/Line Out", "Ground Out"], action) || action.substring(0, 3) == "CS_") {
		advanceOut();
	}
	//message += " <a onclick='undo()' style='cursor:pointer'><u>[Undo]</u></a><p>";
	$("#message").text("");
	$("#message").append(message);
	refreshScoreboard();
}

function appendStats(stat) {
	team = stat.team;
	id = stat.id;
	for (var i = 0; i < playerStats[team].length; i++) {
		if (playerStats[team][i].id == id) {
			playerStats[team][i].stats = playerStats[team][i].stats.concat(stat.stats.slice(0));
			return;
		}
	}
	var newStats = {};
	newStats.team = team;
	newStats.id = id;
	newStats.stats = [];
	for(var i=0; i<stat.stats.length; i++) {
		newStats.stats.push(stat.stats[i]);
	}
	playerStats[team].push(newStats);
}

/*function undo() {
	playerStats = lastPlayStatus.playerStats;
	outs = lastPlayStatus.outs;
	scores = lastPlayStatus.scores;
	inning = lastPlayStatus.inning;
	battingTeam = lastPlayStatus.battingTeam;
	pitchingTeam = lastPlayStatus.pitchingTeam;
	bases = lastPlayStatus.bases;
	currentBatters = lastPlayStatus.currentBatters;
	inningChart = lastPlayStatus.inningChart;
	lineups = lastPlayStatus.lineups;
	pitchers = lastPlayStatus.pitchers;
	refreshScoreboard();
}*/

/*function undo() {
	for (g = 0; g < lastPlay.length; g++) {
		removeStats(lastPlay[g]);
	}
	refreshScoreboard();
}

function removeStats(stat) {
	team = stat.team;
	id = stat.id;
	newstat = jQuery.extend({}, stat);
	for (var i = 0; i < playerStats[team].length; i++) {
		if (playerStats[team][i].id == id) {
			for (j = 0; j < newstat.stats.length; j++) {
				if (playerStats[team][i].stats.indexOf(stat.stats[j]) == -1) {
					$("#message").text("FATAL ERROR: UNABLE TO UNDO");
					return false;
				} else {
					//st = stat.stats[j];
					index = playerStats[team][i].stats.indexOf(stat.stats[j]);
					playerStats[team][i].stats.remove(index);
				}
			}
		}
		return;
	}
}*/