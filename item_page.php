<html>
<body>

<?php

$item_id = "'" . $_GET['id'] . "'";

class SpratlingDB extends SQLite3 {
   function __construct() {
      $this->open('db/SpratlingData1.sqlite');
   }
}
$db = new SpratlingDB();
if(!$db) {
   echo $db->lastErrorMsg();
}

$sql =<<<EOF
   SELECT A.Item_ID, A.Description, A.Identity_Number, A.Dimensions, A.DesignPeriod,
          B.ItemType, 
          C.CatalogNo
   FROM  (tblInventory A LEFT OUTER JOIN lkpItemTypes B ON A.ItemType = B.ItemTypeID)
          LEFT OUTER JOIN tblCatalogItems C ON A.CatalogNo = C.ItemID
   WHERE A.Item_ID = $item_id;
EOF;      
   $item_result = $db->query($sql);
   $item = $item_result->fetchArray(SQLITE3_ASSOC);
   $item_image = strtolower($item['Identity_Number']) . '.jpg';
   $item_type = $item['ItemType'];
   $catalog_number = $item['CatalogNo'];
   $item_dimensions = $item['Dimensions'];
   $item_description = $item['Description'];
   
$sql =<<<EOF
      SELECT B.Material, 
             C.Level
      FROM  (tblInventory_Materials A LEFT OUTER JOIN lkpMaterials B ON A.MaterialID = B.MaterialID)
             LEFT OUTER JOIN lkpLevels C ON A.LevelID = C.LevelID
      WHERE A.Item_ID = $item_id;
EOF;      
   $material_result = $db->query($sql);


$design_period_id = "'" . $item['DesignPeriod'] . "'";
$design_period = '';
if ($design_period_id != '')
{
$sql =<<<EOF
         SELECT *
         FROM  lkpDesignPeriod
         WHERE DesignPeriodId = $design_period_id;
EOF;      
   $design_period_result = $db->query($sql);
   $row = $design_period_result->fetchArray(SQLITE3_ASSOC);
   if ($row)
     $design_period = $row['DesignPeriod'];
}

$material1 = '';
$material2 = '';
$material3 = '';
while($row = $material_result->fetchArray(SQLITE3_ASSOC) ) {
  $level = $row['Level'];
  $material = $row['Material'];
  if ($level == "Primary") {
     $material1 = $material;
  } else if ($level = "Secondary") {
     $material2 = $material;
  } else if ($level == "Tertiary") {
     $material3 = $material;
  }
}

$sql =<<<EOF
      SELECT B.Hallmark, 
             C.Level
      FROM  (tblInventory_Hallmarks A LEFT OUTER JOIN tblHallmarks B ON A.HallmarkID = B.HallmarkID)
             LEFT OUTER JOIN lkpLevels C ON B.Level = C.LevelID
      WHERE A.Item_ID = $item_id;
EOF;      
   $hallmark_result = $db->query($sql);

$hallmark1 = "";
$hallmark1_text = "";
$hallmark2 = "";
$hallmark2_text = "";
$hallmark3 = "";
$hallmark3_text = "";
$hallmark4 = "";
$hallmark4_text = "";
$special_chars = array("(", ")", ":", ";", ".", ",", "-", "/");
while($row = $hallmark_result->fetchArray(SQLITE3_ASSOC) ) {
  $level = $row['Level'];
  $hallmark = $row['Hallmark'];
  $hallmark_clean = str_replace($special_chars, "", $hallmark);
  $hallmark_clean = str_replace(" ", "_", $hallmark_clean);
  $hallmark_clean = strtolower($hallmark_clean);  
  $hallmark_image = "hallmark_" . $hallmark_clean . ".jpg";
  if ($level == "Primary") {
    $hallmark1_text = $hallmark;
    $hallmark1 = $hallmark_image;
  } else if ($level == "Secondary") {
    $hallmark2_text = $hallmark;
    $hallmark2 = $hallmark_image;
  } else if ($level == "Tertiary") {
    $hallmark3_text = $hallmark;
    $hallmark3 = $hallmark_image;
  } else if ($level == "Other") {
    $hallmark4_text = $hallmark;
    $hallmark4 = $hallmark_image;
  }
}

$sql =<<<EOF
      SELECT B.Title, B.FirstName1, B.LastName1, B.FirstName2, B.LastName2, B.PublisherName, B.Notes, B.CopyrightYear
      FROM   tblInventory_Books A LEFT OUTER JOIN Books_Spratling_Catalog_Bibliography B ON A.BookID = B.BookID
      WHERE A.Item_ID = $item_id;
EOF;      
   $books_result = $db->query($sql);


$sql =<<<EOF
      SELECT B.Title, B.FirstName, B.LastName, B.Publication_Issue_Date_Volume, B.Notes
      FROM   tblInventory_Articles A LEFT OUTER JOIN Articles_Auction_Catalog_Bibliography B ON A.ArticleAuction_Catalog_ID = B.ArticleAuction_Catalog_ID
      WHERE A.Item_ID = $item_id;
EOF;      
   $articles_result = $db->query($sql);

  include "sidebar.htm";
?>

<!---- Create item information table ------------------------------------------------->

      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>

	<td valign="top"> 

<table border="0" cellpadding="0" cellspacing="3">
  <tr> 
    <td> 
      <table border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td align="Left"><font face="Arial">
	    <?php
            $FileName = "photos/$item_image";
            if ($item_image != "" && file_exists($FileName)) {
               echo "<img src=\"/photos/$item_image\" alt=\"Photo\" border=\"0\" onload=trapclick()>";
   	    }
	    ?>
            </font>
          </td>
        </tr>
        <tr>
          <td colspan="2">
             <table border="0" cellspacing="0" cellpadding="3">
                <tr> 
                   <td bgcolor="#E0E0E0" Align="right" valign="top"><b><font size="2" face="Arial">Item: </font></b></td>
                   <td valign="top"><font size="2" face="Arial"><?php echo $item_type; ?></font></td>
                </tr>
                <tr> 
                   <td bgcolor="#E0E0E0" Align="right" valign="top" nowrap><b><font size="2" face="Arial">Design Period: </font></b></td>
                   <td valign="top"><font size="2" face="Arial"><?php echo $design_period; ?></font></td>
                </tr>
                <tr> 
                   <td bgcolor="#E0E0E0" Align="right" valign="top"><b><font size="2" face="Arial">Catalog #: </font></b></td>
                   <td valign="top"><font size="2" face="Arial"><?php echo $catalog_number; ?></font></td>
                </tr>
                <tr> 
                   <td bgcolor="#E0E0E0" Align="right" valign="top"><b><font size="2" face="Arial">Dimensions: </font></b></td>
                   <td valign="top"><font size="2" face="Arial"><?php echo $item_dimensions; ?></font></td>
                </tr>
                <tr>
                   <td bgcolor="#E0E0E0" Align="right" valign="top"><font face="Arial" size="2"><b>Description: </b></font></td>
                   <td valign="top"><font size="2" face="Arial"><?php echo $item_description; ?></font></td>
                </tr>
             </table>
           </td>
         </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td> 
      <table width="100%" border="1" cellspacing="0" cellpadding="3">
        <!--- #FFFFE3 --->
        <tr align="center" bgcolor="#FFFFE3"> 
          <td><b><font face="Arial" size="2">Primary Material</font></b></td>
          <td><b><font face="Arial" size="2">Secondary Material 
            </font></b></td>
          <td><b><font face="Arial" size="2">Additional Material</font></b></td>
        </tr>
        <tr align="center"> 
          <td><font face="Arial" size="2"><?php echo $material1; ?>&nbsp;</font></td>
          <td><font face="Arial" size="2"><?php echo $material2; ?>&nbsp;</font></td>
          <td><font face="Arial" size="2"><?php echo $material3; ?>&nbsp;</font></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td> 
      <table width="100%" border="1" cellspacing="0" cellpadding="3">
        <tr align="center" bgcolor="#FFFFE3"> 
          <td><font size="2" face="Arial"><b>Hallmark 1</font></b></font></td>
          <td><font size="2" face="Arial"><b>Hallmark 2</font></b></font></td>
          <td><font size="2" face="Arial"><b>Hallmark 3</font></b></font></td>
          <td><font size="2" face="Arial"><b>Hallmark 4</font></b></font></td>
        </tr>
        <tr align="center"> 
          <td valign="bottom"><font size="2">
	  <?php
            $FileName = "photos/$hallmark1";
            if ($hallmark1 != "" && file_exists($FileName)) {
                    echo "<font size=\"2\" face=\"Arial\"><img src=\"/photos/$hallmark1\" alt=\"Photo\" border=\"0\" onload=trapclick()><br>$hallmark1_text</font>";
            } else {
                    echo "<font size=\"2\" face=\"Arial\">$hallmark1_text</font>";
            }
	  ?>
          </td>
          <td valign="bottom"><font size="2">
	  <?php
            $FileName = "photos/$hallmark2";
            if ($hallmark2 != "" && file_exists($FileName)) {
                    echo "<font size=\"2\" face=\"Arial\"><img src=\"/photos/$hallmark2\" alt=\"Photo\" border=\"0\" onload=trapclick()><br>$hallmark2_text</font>";
            } else {
                    echo "<font size=\"2\" face=\"Arial\">$hallmark2_text</font>";
            }
	  ?>
	  </td>
          <td valign="bottom"><font size="2">
	  <?php
            $FileName = "photos/$hallmark3";
            if ($hallmark3 != "" && file_exists($FileName)) {
                    echo "<font size=\"2\" face=\"Arial\"><img src=\"/photos/$hallmark3\" alt=\"Photo\" border=\"0\" onload=trapclick()><br>$hallmark3_text</font>";
            } else {
                    echo "<font size=\"2\" face=\"Arial\">$hallmark3_text</font>";
            }
	  ?>
	  </td>
          <td valign="bottom"><font size="2">
	  <?php
            $FileName = "photos/$hallmark4";
            if ($hallmark4 != "" && file_exists($FileName)) {
                    echo "<font size=\"2\" face=\"Arial\"><img src=\"/photos/$hallmark4\" alt=\"Photo\" border=\"0\" onload=trapclick()><br>$hallmark4_text</font>";
            } else {
                    echo "<font size=\"2\" face=\"Arial\">$hallmark4_text</font>";
            }
	  ?>
	  </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
          <td valign="center">
           <font face="Arial" size="2">
           Hallmark information for all silver items reflects hallmark data 
              for the specific piece photographed.  If the design was made for 
              an extended period, other examples may have slightly earlier or 
              slightly later marks. Hallmark variation information is included in 
              <a href="/bookstore.htm">Spratling Silver: A Field Guide</a>.
          </font>
          </td>
   </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
  <tr> 
    <td> 
      <table width="100%" border="1" cellpadding="3" cellspacing="0">
        <tr align="center" bgcolor="#9F9F9F"> 
          <td colspan="3" height="28"><b><font face="Arial" size="2">Book References</font></b></td>
        </tr>
        <tr align="center" bgcolor="#E0E0E0"> 
          <td><b><font face="Arial" size="2">Title</font></b></td>
          <td><b><font face="Arial" size="2">Author</font></b></td>
          <td><b><font face="Arial" size="2">Publisher</font></b></td>
        </tr>
	<?php
	  $count = 0;
	  while($row = $books_result->fetchArray(SQLITE3_ASSOC) ) {
	    $title = $row["Title"];
	    $copyright_year = $row["CopyrightYear"];
	    $first_name1 = $row["FirstName1"];
	    $last_name1 = $row["LastName1"];
	    $publisher_name = $row["PublisherName"];
	    echo "<tr>";
            echo "<td valign=\"top\"><font face=\"Arial\" size=\"2\">$title<br>$copyright_year &nbsp;</font></td>";
            echo "<td valign=\"top\"><font face=\"Arial\" size=\"2\">$first_name1 $last_name1 &nbsp;</font></td>";
            echo "<td valign=\"top\"><font face=\"Arial\" size=\"2\">$publisher_name &nbsp;</font></td>";
	    echo "</tr>";
	    $count += 1;
	  }
	  if ($count == 0) {
           echo "<tr><td colspan=\"3\" Align=\"center\"><font face=\"Arial\" size=\"2\">No books found</font></td></tr>";
	  }
	?>

      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" border="1" cellpadding="3" cellspacing="0">
        <tr align="center" bgcolor="#9F9F9F"> 
          <td colspan="3" height="28"><b><font face="Arial" size="2">Article/Auction 
            References</font></b></td>
        </tr>
        <tr align="center" bgcolor="#E0E0E0"> 
          <td><b><font face="Arial" size="2">Title</font></b></td>
          <td><b><font face="Arial" size="2">Author</font></b></td>
          <td><b><font face="Arial" size="2">Publication/Date</font></b></td>
        </tr>

	<?php
	  $count = 0;
	  while($row = $articles_result->fetchArray(SQLITE3_ASSOC) ) {
	    $title = $row["Title"];
	    $first_name = $row["FirstName"];
	    $last_name = $row["LastName"];
	    $pub_date = $row["Publication_Issue_Date_Volume"];
	    echo "<tr>";
            echo "<td valign=\"top\"><font face=\"Arial\" size=\"2\">$title &nbsp;</font></td>";
            echo "<td valign=\"top\"><font face=\"Arial\" size=\"2\">$first_name $last_name &nbsp;</font></td>";
            echo "<td valign=\"top\"><font face=\"Arial\" size=\"2\">$pub_date &nbsp;</font></td>";
	    echo "</tr>";
	    $count += 1;
	  }
	  if ($count == 0) {
           echo "<tr><td colspan=\"3\" Align=\"center\"><font face=\"Arial\" size=\"2\">No articles or auctions found</font></td></tr>";
	  }
	?>
      </table>    </td>
  </tr>

  <tr>
     <td colspan="3" align="center">
               <table>
                  <tr>
                     <td>&nbsp;</td>
                  </tr>
                  <tr>
                     <td><font face="Arial" size="2"><strong>
                      | <a href="search_form.php">Return to Main Search Page</a> |</strong></font></td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                  </tr>
               </table>
    </td>
  </tr>

</table>

<?php $db->close(); ?>

<?php include "sidebar_end.htm"; ?>

</body>
</html>
