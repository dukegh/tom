<html>
<head>
	<link href="css/main.css" rel="stylesheet" type="text/css"/>
</head>
<body onmousemove="mouseMove(event)">
<a id="reloadLink" href="/slide/">Reload</a>

<?php
$dir = "mp3";
$files = scandir("$dir/");
$mp3s = [];
foreach ($files as $file) {
	if (is_dir("$dir/$file")) continue;
	if (is_file("$dir/$file")) {
		$mp3s[] = "'$dir/$file'";
	}
}
?>
<script type="text/javascript">
	var isNetCast = navigator.userAgent.indexOf('LG NetCast') != -1;
	var mp3s = [<?=implode(',', $mp3s)?>];
	var currentPlay = "";
	if (isNetCast && mp3s) {
		currentPlay = mp3s[0];
		document.write('<object type="audio/x-ms-wma" data="' + mp3s[0] + '" width="0" height="0" id="media"></object>');
	}
	var lastMouseMoved = $.now();
	function mouseMove(event) {
		lastMouseMoved = $.now();
		var rl = $("#reloadLink");
		if (rl.css("display") == 'none') {
			rl.html("Reload: <b>" + $("#img" + curImage).attr("src").substr(12) + "</b>");
			rl.css("display", "block");
		}
	}
</script>

<!-- SlidesJS Required: Start Slides -->
<!-- The container is used to define the width of the slideshow -->
<div class="container">
	<div id="slides">
<?php
$dir = "img";
$files = scandir("$dir/");
$images = [];
$i = 0;
foreach ($files as $file) {
	if (is_dir("$dir/$file")) continue;
	if (is_file("$dir/$file") && (
				preg_match('/\.jpg/i', $file) ||
				preg_match('/\.jpeg/i', $file) ||
				preg_match('/\.gif/i', $file) ||
				preg_match('/\.png/i', $file))) {
		$images[] = "'$file'";
		$i++;
		if ($i < 4) echo "<img src=\"image.php?f=$file\" id=\"img$i\">\n";
	}
}
?>
	</div>
</div>
<!-- End SlidesJS Required: Start Slides -->

<!-- SlidesJS Required: Link to jQuery -->
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<!-- End SlidesJS Required -->

<!-- SlidesJS Required: Link to jquery.slides.js -->
<script src="js/jquery.slides.js"></script>
<!-- End SlidesJS Required -->

<!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
<script>
	var curImage = 1;
	var imageArray = [<?=implode(',', $images)?>];
	$(function () {
		$('#slides').slidesjs({
			width: 1280,
			height: 720,
			navigation: {
				effect: "fade"
			},
			pagination: {
				effect: "fade"
			},
			effect: {
				fade: { speed: 4000 },
				slide: { speed: 2000 }
			},
			play: {
				active: true,
				auto: true,
				interval: 10000,
				swap: true,
				effect: "fade"
			}
		});
	});
</script>
<!-- End SlidesJS Required -->

<script type="text/javascript">

function playNext() {
	if (currentPlay) {
		var i = mp3s.length + 1;
		while (i--) {
			if (mp3s[i - 1] == currentPlay) {
				if (i == mp3s.length) i = 0;
				currentPlay = mp3s[i];
				var m = getMedia();
				m.data = currentPlay;
				m.play(1);
			}
		}
	}
}

function getMedia() {
	return document.getElementById("media");
}

function refreshMediaControl() {
	if (lastMouseMoved + 2000 < $.now()) $("#reloadLink").css("display", "none");
	var m = getMedia();
	if (! m) return;
	var pi = m.mediaPlayInfo();
	if (m.playState == 5 && typeof playNext != 'undefined') playNext();
}

var tmOut = setInterval("refreshMediaControl();", 1000);
</script>

</body>
</html>
