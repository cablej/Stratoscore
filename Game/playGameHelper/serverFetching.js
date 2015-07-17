function initGame(GAME_ID) {
	time = new Date();
	$.ajax({
		type: "POST",
		url: "gameTools.php",
		data: "id=" + GAME_ID + "&type=gameInfo",			  
		success: function(xml) {
			jxml = $.parseXML(xml);
			$xml = $(jxml);
			$game = $xml.find("game");
			finished = $game.attr("finished");
			$.ajax({
				type: "POST",
				url: "gameTools.php",
				data: "id=" + GAME_ID + "&type=getPlayers",
				success: function(team_xml) {
					arr = team_xml.split("<|>");
					for(i=0; i<arr.length; i++) {
						xml_doc = arr[i];
						jquery = $.parseXML(xml_doc);
						$team_xml = $(jquery);
						team = "team" + (i+1);
						table = "#"+team+"_table";
						team_name = $xml.find("team").eq(i).attr("name");
						team_names[i] = team_name;
						$("#"+team+"_name").text(team_name);
						$team_xml.find("players").find("player").each(function(){
							var $player = $(this);
							var id = $player.attr('id');
							var first_name = $player.attr("first_name");
							var last_name = $player.attr("last_name");
							var position = $player.find("position").text();
							var rest = $player.find("rest").text();
							rosters[i].push(id);
							full_name = first_name + " " + last_name;
							ids_toName[i][id] = full_name;
							positions[i][id] = position;
							restings[i][id] = rest;
							if(finished == "false") {
								$(table).append("<tr><td><a style='cursor:pointer' onclick='addToLineup("+i+", "+id+")'><u>"+full_name+"</u></a></td><td>"+position.substr(0, 2)+"</td><td><p><a style='cursor:pointer' target='_blank' href='../Standings/Team/Player/displayPlayer.php?team="+team_name+"&id="+id+"'><u>[View]</u></a></p></td><td>"+id+"</td></tr>");
							}
						});
						if(finished == "false") {
							checkDisabled();
							refreshLineup(0, true);
							refreshLineup(1, true);
							$("#load").hide();
							$("#actionsDiv").hide();
							$("#scoreboard").hide();
							$("#game").show();
						}
					}
				},
				error: function(team_xml){
					alert("Could not retrieve XML file.");
				}
			});
			if($game.attr("finished") == "true") {
				setTimeout(function(){
					window.location = "../Stats/displayGameStats.php?game=" + GAME_ID;
					/*$("#load").hide();
					$("#results").show();
					updateResults($game);*/
				}, 250);
			}
		},
		error: function(return_Data){
			alert("Could not retrieve XML file.");
		}
	});
	
	fetchStatCategories();
	
	fetchAbbreviations();
	
	difference = Math.abs(new Date() - time);
	$("#status").text("Process complete. ("+difference+" ms)");
}

function fetchStatCategories() {
	$.ajax({
		type: "POST",
		url: "gameTools.php",
		data: "id=" + GAME_ID + "&type=getStatCats",
		success: function(statCategories) {
			var categories_array = statCategories.split("<|>");
			categories[0] = categories_array[0].split("~");
			categories[1] = categories_array[1].split("~");
		},
		error: function(statCategories){
			alert("Could not retrieve XML file.");
		}
	});
}
	
function fetchAbbreviations() {
	$.ajax({
		type: "POST",
		url: "gameTools.php",
		data: "id=" + GAME_ID + "&type=getStatAbbreviations",
		success: function(statCategories) {
			var abbreviations_array = statCategories.split("<|>");
			abbreviations[0] = abbreviations_array[0].split("~");
			abbreviations[1] = abbreviations_array[1].split("~");
		},
		error: function(statCategories){
			alert("Could not retrieve XML file.");
		}
	});
}	

function updateResults($game) {
	$("#inningChart").empty();
	$("#game").hide();
	
	teamNames = [];
	teamScores = [];
	
	$game.find("team").each(function(){
		$team = $(this);
		teamNames.push($team.attr("name"));
		teamScores.push($team.find('score').text());
	});

	configureFinalScore();
	configureInningChart();

	configureGameStats();
}

function configureGameStats() {
	for(var i=0; i<teamNames.length; i++) {
		var id = "#team" + (i+1) + "_playerStats";
		$(id).empty();
		var str = "<tr><th>Name</th><th>Position</th>";
		for(var j=0; j<abbreviations.length; j++) {
			var type = j === 0 ? "Batting" : "Pitching";
			str += "<th onclick='toggleColumn(\""+ type +"\", " + (i+1) + ")'>" + type + "</th>";
			for(var h=0; h<abbreviations[j].length; h++) {
				str += "<th style='display:none' class='"+ type + (i+1) +"'>" + abbreviations[j][h] + "</th>";
			}
		}
		str += "</tr>";
		$(id).append(str);
		var num = 0;
		$game.find("team").each(function() {
			if(num == i) {
				$team = $(this);
				$team.find("stats").find("player").each(function(){
					var $player = $(this);
					var playerID = $player.attr("id");
				//	alert(ids_toName[i][playerID] + " " + playerID);
				});
			}
			num++;
		});
		
	}
}

function configureFinalScore() {
	if(teamScores[0] > teamScores[1]) {
		$("#finalScore").text(teamNames[0] + " " + teamScores[0] + ", " + teamNames[1] + " " + teamScores[1]);
	} else {
		$("#finalScore").text(teamNames[1] + " " + teamScores[1] + ", " + teamNames[0] + " " + teamScores[0]);
	}
}

function configureInningChart() {
	var inningChart = [[], []];
	$game.find("innings").find("inning").each(function(){
		var $inning = $(this);
		var topInning = $inning.find('top').text();
		var bottomInning = $inning.find('bottom').text();
		inningChart[1].push(topInning);
		inningChart[0].push(bottomInning);
	});
	
	var maxLength = inningChart[0].length > inningChart[1].length ? inningChart[0].length : inningChart[1].length;
	
	var header = "<tr><th></th>";
	for(var i=0; i<maxLength; i++) {
		header += "<th>" + (i+1) + "</th>";
	}
	header += "<th>Score</th></tr>";
	$("#inningChart").append(header);
	
	for(var i=inningChart.length-1; i>=0; i--) {
		var str = "<tr><th>" + teamNames[i] + "</th>";
		for(var j=0; j<inningChart[i].length; j++) {
			str += "<td>" + inningChart[i][j] + "</td>";
		}
		str += "<td>" + teamScores[i] + "</td></tr>";
		$("#inningChart").append(str);
	}
}
	
function submitStats(statXML) {
	$.ajax({
		type: "POST",
		url: "gameTools.php",
		data: "id=" + GAME_ID + "&type=submitStats" + "&xml="+statXML,
		success: function(submitted) {
			return true;
		},
		error: function(){
			alert("[ERROR]: Could not send data to server. Please try again.");
		}
	});
}

function submitEndGame() {
	$.ajax({
		type: "POST",
		url: "gameTools.php",
		data: "id=" + GAME_ID + "&type=endGame" + "&team1="+scores[0] + "&team2="+scores[1] + "&team1_chart="+inningChart[0].join()+"&team2_chart="+inningChart[1].join(),
		success: function(state) {
			return true;
		},
		error: function(state){
			alert("[ERROR]: Could not send data to server. Please try again.");
			return false;
		}
	});
}
