<?php

/***  траектории энергии поля для совмещенного движ. заряда и магн. момента ***/

/***** маштабирование картинки   ****/

$scale = 5;

#вклады заряда и м. момента
$m_factor = 1000;#множитель магн. момента
$e_part = 1;
$m_part = 1;


$x_max = $scale*3000;
$y_max = 1000;

#$x_shift = 1500;
$x_shift = 500;
$tr_shift = $scale*1500;#смещение для проекции (y,z)

$im = imagecreate($x_max, $x_max);
$background = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$blue = imagecolorallocate($im, 0, 0, 255);
$green = imagecolorallocate($im, 0, 255, 0);
$red = imagecolorallocate($im, 255, 0, 0);#центр

#############
$v = 0.5;#скорость электрона
$N=10000;#к-во итераций
$dt = 0.2;#приращение времени в одной итерации


#оси х,y
#axis();


############

#траектория элемента объемя энергия в обоих направлениях от точки на поперечнике
#z-координата не изображается на графике; график дяёт только xy проекцию движения поля

#line2($t=0,$x=100,$y=300,$z=0,$black,0);




line($t=0,$x=0,$y=300,$z=0,1,$black);



#output
header('Content-Type: image/png');
imagepng($im);


### functions #####

function bigpixel($x,$y,$color) {
	global $im,$scale;
	imagesetpixel($im, $scale*$x,$scale*cv($y), $color);
	imagesetpixel($im, $scale*$x+1, $scale*cv($y), $color);
	imagesetpixel($im, $scale*$x-1, $scale*cv($y), $color);
	imagesetpixel($im, $scale*$x, $scale*cv($y+1), $color);
	imagesetpixel($im, $scale*$x, $scale*cv($y-1), $color);
}

#reflect y- coordinate to show normal orientation graph
function cv($y) {
	global $y_max;
	return $y_max - $y;
}

function line($t,$x,$y,$z,$dir,$color,$y_shift=0,$xe0=0) {
	#$t,$x,$y - начальные значения
	#$dir = +-1 направление линии - вперед или назад
	#$xe0 -  нач. точка е-на

	
	global $N,$x_shift,$tr_shift, $red, $v, $dt, $e_part, $m_part, $m_factor;
	
	$v2 = $v*$v;

	#electron - nach. tochka
	$xe = $xe0 + $dir*$v*$t;
	
	#time moments
	for ($i=1;$i<=$N;$i++) {
		
		

#echo "<br>i=$i; \$x=$x,\$y=$y,\$z=$z,1,\$blue,0,\$xe=$xe";


		
				
		#electron
		#$xe = $dir*$v*$t;
		
		/*** продольная проекция (х,y) ***/		
		#електрон
		bigpixel($xe+$x_shift,1,$red);	
		
		#energy volume point
		bigpixel($x+$x_shift,$y+$y_shift,$color);
				
		
		/*** поперечная проекция (х,y) ***/
		#bigpixel($y+$tr_shift,$z,$color);
		
		#new values
		$x2 = ($x-$xe)*($x-$xe);
		$y2 = $y*$y;
		$z2 = $z*$z;		
		$r2 = $x2+$y2+$z2;
		$r = sqrt($r2);
		$vr = $v*($x-$xe);
		$vr2 = $vr*$vr;
		$vrV2 = $v2*($y2+$z2);#квадрат вект произв. v на r
		$al = (1-$v2)/pow(($r2 - $vrV2),3/2);
		
		$gm2  = 1/(1-$v2);
		$ksi2 = 1/(1- $v2 + ($v2*$x2)/$r2 );
		

		
		/***  поле заряда  ***/
		
		# V стр.105
		#импульс-энергия поля заряда
		$pex = 2*$al*$al*($v*$r2 - $x2*$v);#x-импульс от заряда
		$pey = 2*$al*$al*(-$y*($x-$xe)*$v);#попер. y-импульс от заряда
		$pez = 2*$al*$al*(-$z*($x-$xe)*$v);#попер. z-импульс от заряда
		$ee = $al*$al*($r2*(1+$v2) - $v2*$x2);#энергия от заряда


		/*		
		#Ux=ux - для сверки
		$Ux = $pex/$ee;
		$Uy = $pey/$ee;		
		
		$Q = $x*$x + $y*$y*(1+$v*$v) - 2*$x*$xe + $v*$v*$t*$t;	
		$ux = $dir*2*$v*$y*$y/$Q; #dx/dt	
		$uy = $dir*2*$v*$y*($xe - $x)/$Q; #dy/dt
		$uz = 0;#dz/dt
		//end of charge
		*/

		
		/*** поле момента  ***/

		$A = 3*$vr*pow($ksi2,5)/(pow($gm2,3)*pow($r2,5)*$v2);
		$b = $v2*$r2*(1-$v2) + $vr2*(4*$v2-3);
		$c = pow($ksi2,3)/(2*pow($gm2,4)*$v2*pow($r2,5));
	
		
		#импульс-энергия поля момента
		$pmx = $m_factor*$A*( $v*$vr*(2*(1-$v2)*$r2 - $vr2  )  + ($x-$xe)*$b );
		$pmy = $m_factor*$A*$y*$b;
		$pmz = $m_factor*$A*$z*$b;
		$em = $m_factor*$c*( 9*pow($ksi2,2)*$vr2*($r2*($v2+1) - $vr2)  - 6*$gm2*$r2*$vr2*$ksi2 +  pow($gm2,2)*pow($r2,2)*$v2 );
		
		/*
		#сверка
		$Ux = $pmx/$em;
		$Uy = $pmy/$em;
		
		$Q = 9*$ksi2*$ksi2*$v2*$x2*( $r2*($v2+1) - $v2*$x2) - 6*$gm2*$r2*$v2*$x2*$ksi2 + $gm2*$gm2*$r2*$r2*$v2;		
		$P = 6*$v*($x-$xe)*$ksi2*$ksi2*$gm2/$Q;
		
		$ux = $dir*3*($x-$xe)*$y*$y*$v2*(1-$v2)*$P; #dx/dt	
		$uy = $dir*$y*$v2*($x2*(3*$v2-2) + $y*$y*(1-$v2)) *$P; #dy/dt						
		$uz = 0;
		#echo "<br>i=$i; t=$t; x=$x; y=$y; ux: $ux; Ux: $Ux; uy:$uy; Uy:$Uy;";	
		*/		

	
		#импульс - совместное суммарное поле без взаимодействия
		$px0 = $e_part*$pex + $m_part*$pmx;
		$py0 = $e_part*$pey + $m_part*$pmy;
		$pz0 = $e_part*$pez + $m_part*$pmz;

		#импульс от взаимод. - множитель
		$pI = $m_factor*pow((1-$v2),2)/(pow($r,5)*sqrt($r2-$vrV2));

		#полное суммарное поле с взаимодействием
		$px = $px0;
		$py = $py0 - $pI*$z;
		$pz = $pz0 + $pI*$y;
		
		
		$e = $e_part*$ee + $m_part*$em;

		$ux0 = $ux;
		$uy0 = $uy;
		$uz0 = $uz;

		
		$ux = $dir*$px/$e;
		$uy = $dir*$py/$e;
		$uz = $dir*$pz/$e;;

#echo "<br>x=$x, y=$y, z=$z; ux=$ux; uy=$uy; uz=$uz;";

#выход на трубу-спираль
#echo "<br>r=$r, t=$t, x=$x, y=$y, z=$z";
if (abs($r-$r0)<0.001) {
	#echo "<br><font color=red>трубааа</font>";
	#echo "<br>r=$r, t=$t, x=$x, y=$y, z=$z, xe=$xe";
}


if ($uy0>0 && $uy<0) {		
	$xv0 = $xv;
	$xv = $x;
	$dxv = $xv-$x0;
	#echo "<br>вершина: r=$r, t=$t, x=$x, y=$y, z=$z, dx=$dxv";
}
	

#момент импульса
$m = abs($py*$z - $pz*$y);
#echo "<br>r=$r, t=$t, m=$m;";


	
		####next point
		$t = $t + $dt;
		$x = $x + $ux * $dt;
		$y = $y + $uy * $dt;
		$z = $z + $uz * $dt;

		$r0 = $r;

		$xe = $xe + $dir * $v * $dt;

		
	}//end of time moments loop	
	
}//end of line()

function line2($t,$x,$y,$z,$color,$y_shift=0) {
	line($t,$x,$y,$z,$dir=1,$color,$y_shift);
	line($t,$x,$y,$z,$dir=-1,$color,$y_shift);
}

function axis() {
	global $blue,$x_shift;
	
	$shift =400;
	
	$d = 1;

	#x-axis
	for ($i=0;$i<=100;$i++) {
		$x = $x_shift + $i*$d + $shift;
		bigpixel($x,$y+100,$blue);	
	}

	
	#y-axis
	for ($i=0;$i<=100;$i++) {
		$y = $i*$d;
		bigpixel($x_shift+$shift,$y,$blue);	
	}
}