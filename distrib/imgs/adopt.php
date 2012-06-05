<?php if (!defined('insite')) die("no access"); 
/*
     	  items add opt for Season 5		   
         ver: 1.3[27.07.2010]	       
               by epmak              
*/
/*<Ancients sets name>*/
// "a" - old sets( s1, s2, s3) "na" - new sets since 4s
$anc["a"][0][2]="Ceto"; // rapier
$anc["a"][0][14]="Hyon";	//ligtning sword
$anc["a"][2][1]="Warrior";	//morning star
$anc["a"][4][5]="Gaion";
$anc["a"][4][9]="Gaia";
$anc["a"][5][0]="Apollo";	//staff
$anc["a"][6][6]="Hera";		//shield
$anc["a"][6][9]="Eplete";
$anc["a"][7][1]="Hyon"; 	$anc["na"][7][1]="Vicious";	//dragon
$anc["a"][7][2]="Apollo"; 	//pad
$anc["a"][7][3]="Anubis";	$anc["na"][7][3]="Isis"; //legendary	
$anc["a"][7][5]="Warrior";	//leather
$anc["a"][7][6]="Eplete";	//scale
$anc["a"][7][7]="Hera";		//sphinx
$anc["a"][7][10]="Ceto";	//vine
$anc["a"][7][11]="Gaia";	//silk
$anc["a"][7][12]="Odin";	//wind
							$anc["na"][7][14]="Aruane";	//guardian
$anc["a"][7][26]="Agnis";	//adamantine
$anc["a"][7][40]="Krono";	$anc["na"][7][40]="Semeden";		//red wing

$anc["a"][8][0]="Hyperio";	//bronze						
							$anc["na"][8][1]="Vicious";	//dragon						
$anc["a"][8][2]="Apollo";	//pad						
$anc["a"][8][3]="Anubis";	$anc["na"][8][3]="Isis";	//legendary						
$anc["a"][8][4]="Evis";		//bone						
$anc["a"][8][5]="Warrior";	//leather						
$anc["a"][8][6]="Eplete";	//scale						
$anc["a"][8][7]="Hera";		//sphinx						
$anc["a"][8][8]="Garuda";	//brass						
$anc["a"][8][9]="Kantata";	//plate					
$anc["a"][8][11]="Gaia";	//silk					
$anc["a"][8][12]="Odin";	//wind				
$anc["a"][8][13]="Argon";	//spirit				
$anc["a"][8][14]="Gywen";	$anc["na"][8][14]="Aruane";//guardian		
$anc["a"][8][15]="Gaion";	$anc["na"][8][15]="Muren";//unicorn
$anc["a"][8][26]="Agnis";	//adamantine
							$anc["na"][8][40]="Semeden";	//red wing
$anc["a"][9][0]="Hyperio";
							$anc["na"][9][1]="Vicious";
$anc["a"][9][2]="Apollo";
							$anc["na"][9][3]="Isis";
$anc["a"][9][4]="Evis";
$anc["a"][9][5]="Warrior";
$anc["a"][9][6]="Eplete";
$anc["a"][9][7]="Hera";
$anc["a"][9][8]="Garuda";
$anc["a"][9][10]="Ceto";
$anc["a"][9][11]="Gaia";
$anc["a"][9][12]="Odin";
$anc["a"][9][13]="Argon";
							$anc["na"][9][14]="Aruane";
$anc["a"][9][15]="Gaion";	$anc["na"][9][15]="Muren";
$anc["a"][9][26]="Agnis";	$anc["na"][9][26]="Browii";
$anc["a"][9][40]="Krono";
	
$anc["a"][10][1]="Hyon";	
$anc["a"][10][2]="Apollo";	
$anc["a"][10][3]="Anubis";	
$anc["a"][10][5]="Warrior";	
$anc["a"][10][7]="Hera";	
$anc["a"][10][7]="Garuda";	
$anc["a"][10][9]="Kantata";	
$anc["a"][10][10]="Ceto";	
$anc["a"][10][11]="Gaia";	
$anc["a"][10][12]="Odin";	
$anc["a"][10][13]="Argon";	
$anc["a"][10][14]="Gywen";	
							$anc["na"][10][15]="Muren";
							$anc["na"][10][26]="Browii";
$anc["a"][10][40]="Krono";	$anc["na"][10][40]="Semeden";							
$anc["a"][11][0]="Hyperio";								
$anc["a"][11][1]="Hyon";								
							$anc["na"][11][3]="Isis";
$anc["a"][11][4]="Evis";							
$anc["a"][11][5]="Warrior";							
$anc["a"][11][7]="Hera";							
$anc["a"][11][9]="Kantata";							
$anc["a"][11][10]="Ceto";							
$anc["a"][11][12]="Odin";							
$anc["a"][11][14]="Gywen";	$anc["na"][11][14]="Aruane";							
$anc["a"][11][15]="Gaion";						
							$anc["na"][11][26]="Browii";
							$anc["na"][11][40]="Semeden";
$anc["a"][13][8]="Warrior";							
$anc["a"][13][9]="Kantata";	$anc["na"][13][9]="Agnis";						
$anc["a"][13][21]="Anubis";	$anc["na"][13][21]="Muren";						
$anc["a"][13][22]="Ceto";	$anc["na"][13][22]="Vicious";						
$anc["a"][13][23]="Kantata";							
$anc["a"][13][24]="Apolo";	$anc["na"][13][24]="Krono";							
$anc["a"][13][12]="Eplete";							
$anc["a"][13][13]="Garuda";							
$anc["a"][13][25]="Apolo";	$anc["na"][13][25]="Browii";							
$anc["a"][13][23]="Evis";							
$anc["a"][13][27]="Gaion";							
$anc["a"][13][28]="Gywen";							
	
/*<Harmony options>*/

/*weapons*/
$wep[1]=" Min. Attack Power +"; $wpoints[1]=1;
$wep[2]=" Max. Attack Power +";	$wpoints[2]=1;
$wep[3]=" Need Strength -";		$wpoints[3]=2;
$wep[4]=" Need Agility -";		$wpoints[4]=2;
$wep[5]=" Attack (Max, Min) +";	$wpoints[5]=1;
$wep[6]=" Critical Damage +";	$wpoints[6]=2;
$wep[7]=" Skill Power +";		$wpoints[7]=2;
$wep[8]=" Attack Rate +";		$wpoints[8]=2;
$wep[9]=" SD Rate +";			$wpoints[9]=2;
$wep[10]=" SD Ignore Rate +";	$wpoints[10]=1;

/*staffs*/
$staffs[1]=" Magic Power +";		$spoints[1]=2;
$staffs[2]=" Need Strength -";		$spoints[1]=2;
$staffs[3]=" Need Agility -";		$spoints[1]=2;
$staffs[4]=" Skill Power +";		$spoints[1]=3;
$staffs[5]=" Critical Damage +";	$spoints[1]=2;
$staffs[6]=" SD Rate +";			$spoints[1]=2;
$staffs[7]=" Attack Rate +";		$spoints[1]=2;
$staffs[8]=" SD Ignore Rate +";		$spoints[1]=1;
/*armors*/
$armors[1]=" Defence Power +";		$apoints[1]=2;
$armors[2]=" Max. AG +";			$apoints[1]=2;
$armors[3]=" Max. HP +";			$apoints[1]=2;
$armors[4]=" HP Auto Rate +";		$apoints[1]=1;
$armors[5]=" MP Auto Rate +";		$apoints[1]=1;
$armors[6]=" Def. Success Rate +";	$apoints[1]=1;
$armors[7]=" Dmg Rate +";			$apoints[1]=1;
$armors[8]=" SD Rate +";			$apoints[1]=1;

?>