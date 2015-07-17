<html>
<head>
<title>Create Team</title>
</head>
<body>
<center>
<div class='buttonMenu'>
	<a href='..' style='border-left:0px;'>Home</a>
	<a href='index.php' class='currentPage'>Standings</a>
	<a href='../schedule.php'>Schedule</a>
	<a href='../Stats'>Stats</a>
</div>
<?php
    $prefix = "../";
    
    include $prefix . "XMLTools.php";
    
    if(isSet($_GET['name']) && isSet($_GET['division']) && isSet($_GET['owner'])) {
        $league = getXMLatURL($leagueFile, true);
        $league = createTeam($league, $_GET['name'], $_GET['owner'], $_GET['img'], $_GET['division'], $_GET['password']);
        saveXMLAtURL($leagueFile, $league, true);
        header("Location: index.php");
    }
    else {
    }
?>
<br>
<form method='GET' action=''>
<p style='display:inline; border-radius:3px'>New team: </p><input type='text' id='name' name='name' placeholder='Team Name'></input>
<input type='text' id='division' name='division' placeholder='Division'></input>
<input type='text' id='owner' name='owner' placeholder='Owner'></input>
<input type='text' id='img' name='img' placeholder="Paste the team's icon url here."></input>
<input type='password' id='password' name='password' placeholder='Password'></input>
<input type='submit'></input>
</form>
<form method='GET' action='importTeam.php'>
<p style='display:inline; border-radius:3px;'>MLB Rosters: </p><select name='team'>
    <option disabled selected style='color:black;'>Select a team</option>
    <optgroup label="AL East">
	<option title='Orioles' value='bal'>Orioles</option>
	<option title='Red Sox' value='bos'>Red Sox</option>
	<option title='Yankees' value='nyy'>Yankees</option>
	<option title='Rays' value='tb'>Rays</option>
	<option title='Blue Jays' value='tor'>Blue Jays</option>
	</optgroup>
    <optgroup label="AL Central">
	<option title='White Sox' value='chw'>White Sox</option>
	<option title='Indians' value='cle'>Indians</option>
	<option title='Tigers' value='det'>Tigers</option>
	<option title='Royals' value='kc'>Royals</option>
	<option title='Twins' value='min'>Twins</option>
	</optgroup>
    <optgroup label="AL West">
	<option title='Astros' value='hou'>Astros</option>
	<option title='Athletics' value='oak'>Athletics</option>
	<option title='Mariners' value='sea'>Mariners</option>
	<option title='Rangers' value='tex'>Rangers</option>
	</optgroup>
    <optgroup label="NL East">
	<option title='Braves' value='atl'>Braves</option>
	<option title='Marlins' value='mia'>Marlins</option>
	<option title='Mets' value='nym'>Mets</option>
	<option title='Phillies' value='phi'>Phillies</option>
	<option title='Nationals' value='was'>Nationals</option>
	</optgroup>
    <optgroup label="NL Central">
	<option title='Cubs' value='chc'>Cubs</option>
	<option title='Reds' value='cin'>Reds</option>
	<option title='Brewers' value='mil'>Brewers</option>
	<option title='Pirates' value='pit'>Pirates</option>
	<option title='Cardinals' value='stl'>Cardinals</option>
	</optgroup>
    <optgroup label="NL West">
	<option title='Diamondbacks' value='ari'>Diamondbacks</option>
	<option title='Rockies' value='col'>Rockies</option>
	<option title='Dodgers' value='lad'>Dodgers</option>
	<option title='Padres' value='sd'>Padres</option>
	<option title='Giants' value='sf'>Giants</option>
	</optgroup>
<input type='text' id='name' name='name' placeholder='Team Name'></input>
<input type='text' id='division' name='division' placeholder='Division'></input>
<input type='text' id='owner' name='owner' placeholder='Owner'></input>
<input type='password' id='password' name='password' placeholder='Password'></input>
<input type='submit' value="Import"></input>
</select>

</form>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>