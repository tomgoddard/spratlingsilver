<html>
<head>
<title>SpratlingSilver.com - Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#FFFFFF" background="images/backgrnd.gif">

<a name="top"></a> 

<?php
   class MyDB extends SQLite3 {
      function __construct() {
         $this->open('db/SpratlingData1.sqlite');
      }
   }
   $db = new MyDB();
   if(!$db) {
      echo $db->lastErrorMsg();
   }

  include "sidebar.htm";
?>

<table border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
      <table border="0" cellspacing="0" cellpadding="5" align="Center"> 
        <tr>
          <td align="center"><b><font size="3" face="Arial" color="#990000">
            <div align="center"><font size="3" face="Arial, Helvetica, sans-serif"><b>Spratling 
              Database Search</b></font></div>
          </td>
        </tr>
      </table>    
      <p></p>
      <form method="post" action="search_results.php" name="">
        <table border="0" cellspacing="0" cellpadding="2">
                <tr valign="top"> 
                  <td align="right"><font size="2" face="Arial"><b>Item:</b>
		   <br>What is it? </font></td>
                  <td> <font size="2" face="Arial"> 
			   <select name="ItemType[]" size="4" multiple>
				  <option selected value="All">All Items
<?php
$sql =<<<EOF
   SELECT * FROM lkpItemTypes
   WHERE ExcludeOnWebsite='False'
   ORDER BY ItemType COLLATE NOCASE ASC;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
      echo "<option value=". $row['ItemTypeID'] .">". $row['ItemType'] ."</option>";
   }
?>
			   </select>  
                    </font>
		    <br><font size="1">Multiple select ok</font>
		    </td>
                </tr>
		<tr><td><p></p></tr>
                <tr valign="top"> 
                  <td align="right"><font size="2" face="Arial"><b>Material:</b> <br>What is it made of? </font></td>
                  <td> <font size="2" face="Arial"> 
			   <select name="Materials[]" size="4" multiple>
				  <option selected value="All">All Materials&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<?php
$sql =<<<EOF
   SELECT * FROM  lkpMaterials
   WHERE ExcludeOnWebsite='False'
   ORDER BY Material COLLATE NOCASE ASC;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
      echo "<option value=". $row['MaterialID'] .">". $row['Material'] ."</option>";
   }
?>
			   </select> 
                    </font></td>
                </tr>
		<tr><td><p></p></tr>
                <tr valign="top"> 
                  <td align="right"><font size="2" face="Arial"><b>Primary Hallmark:</b>
                    </font></td>
                  <td> <font size="2" face="Arial"> 
			   <select name="Primary_Hallmark[]" size="4" multiple>
				  <option selected value="All">All Hallmarks

<?php
$sql =<<<EOF
   SELECT A.HallmarkID, A.Hallmark, B.Level 
   FROM  tblHallmarks A, lkpLevels B
   WHERE A.ExcludeOnWebsite='False'
     AND A.level=B.LevelID
     AND B.level='Primary'
   ORDER BY B.Level, A.Hallmark COLLATE NOCASE ASC;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
      echo "<option value=". $row['HallmarkId'] .">". $row['Hallmark'] ."</option>";
   }
?>
			   </select> 
                    </font></td>
                </tr>
		<tr><td></tr>
                <tr valign="top"> 
                  <td align="right"><font size="2" face="Arial"><b>Secondary Hallmark:</b> <br>Place  
                    </font></td>
                  <td> <font size="2" face="Arial"> 
			   <select name="Secondary_Hallmark[]" size="4" multiple>
				  <option selected value="All">All Hallmarks

<?php
$sql =<<<EOF
   SELECT A.HallmarkID, A.Hallmark, B.Level 
   FROM  tblHallmarks A, lkpLevels B
   WHERE A.ExcludeOnWebsite='False'
     AND A.level=B.LevelID
     AND B.level='Secondary'
   ORDER BY B.Level, A.Hallmark COLLATE NOCASE ASC;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
      echo "<option value=". $row['HallmarkId'] .">". $row['Hallmark'] ."</option>";
   }
?>
			   </select> 
                    </font></td>
                </tr>
		<tr><td></tr>
                <tr valign="top"> 
                  <td align="right"><font size="2" face="Arial"><b>Tertiary Hallmark:</b> <br>Silver Content  
                    </font></td>
                  <td> <font size="2" face="Arial"> 
			   <select name="Third_Hallmark[]" size="4" multiple>
				  <option selected value="All">All Hallmarks&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<?php
$sql =<<<EOF
   SELECT A.HallmarkID, A.Hallmark, B.Level 
   FROM  tblHallmarks A, lkpLevels B
   WHERE A.ExcludeOnWebsite='False'
     AND A.level=B.LevelID
     AND B.level='Tertiary'
   ORDER BY B.Level, A.Hallmark COLLATE NOCASE ASC;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
      echo "<option value=". $row['HallmarkId'] .">". $row['Hallmark'] ."</option>";
   }
?>
			   </select> 
                    </font></td>
                </tr>
		<tr><td></tr>
                <tr valign="top"> 
                  <td align="right"> 
                    <p><font size="2" face="Arial"><b>Additional Hallmark:</b> </font></p>
                    </td>
                  <td> <font size="2" face="Arial"> 
			   <select name="Additional_Hallmarks[]" size="4" multiple>
				  <option selected value="All">All Hallmarks&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
$sql =<<<EOF
   SELECT A.HallmarkID, A.Hallmark, B.Level 
   FROM  tblHallmarks A, lkpLevels B
   WHERE A.ExcludeOnWebsite='False'
     AND A.level=B.LevelID
     AND B.level='Other'
   ORDER BY B.Level, A.Hallmark COLLATE NOCASE ASC;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
      echo "<option value=". $row['HallmarkId'] .">". $row['Hallmark'] ."</option>";
   }
?>
			   </select> 
                    </font></td>
                </tr>
                <tr> 
                  <td width="50"><font face="Arial"></font></td>
                  <td><font face="Arial"></font></td>
                </tr>
                <tr> 
                  <td colspan="2"><font face="Arial"></font> 
                    <table border="0" cellspacing="0" cellpadding="5" align="center">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit" value="Search">
                        </td>
                        <td>&nbsp;</td>
                        <td> 
                          <input type="reset" name="Submit2" value="Reset">
                        </td>
                        <td align=center width=80>
                          <a href="/search_help.htm"><font face="Arial" size=2>Help</font></a>
                        </td>
                        <td>
                          <a href="/search_catnum.php"><font face="Arial" size=2>Search by Spratling catalog number</font></a>
                        </td>
                      </tr>
                    </table>
                    <div align="center"><font face="Arial"></font></div>
                  </td>
                </tr>
              </table>
            </form>
            
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
          </td>
        </tr>
      </table>
</table>

<?php include "sidebar_end.htm"; ?>

</body>
</html>
