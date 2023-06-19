<html>
<body>

<?php
   class SpratlingDB extends SQLite3 {
      function __construct() {
         $this->open('db/SpratlingData1.sqlite');
      }
   }
   $db = new SpratlingDB();
   if(!$db) {
      echo $db->lastErrorMsg();
   }

if (array_key_exists("catalog_number", $_POST))
{

// Search by Spratling wholesale catalog number

$catalog_num = '"' . $_POST["catalog_number"] . '%"'; 

$sql =<<<EOF
      SELECT * 
      FROM   tblCatalogItems A, tblInventory B
      WHERE (A.CatalogNo LIKE $catalog_num OR A.AltCatalogNo LIKE $catalog_num)
        AND B.Website = "True"
        AND A.ItemID = B.CatalogNo
      ORDER BY B.CatalogSort, A.CatalogNo, B.ItemType;
EOF;
   $dbresult = $db->query($sql);

} else {

  // Search by item type, material, and hallmarks.
	 
  $ItemTypes = $_POST["ItemType"];
  $MaterialList = $_POST["Materials"];
  $PrimaryHallmarks = $_POST["Primary_Hallmark"];
  $SecondaryHallmarks = $_POST["Secondary_Hallmark"];
  $ThirdHallmarks = $_POST["Third_Hallmark"];
  $AdditionalHallmarks = $_POST["Additional_Hallmarks"];

  if (count($ItemTypes) >= 1 && $ItemTypes[0] == "All")
    unset($ItemTypes[0]);
  if (count($MaterialList) >= 1 && $MaterialList[0] == "All")
    unset($MaterialList[0]);
  if (count($PrimaryHallmarks) >= 1 && $PrimaryHallmarks[0] == "All")
    unset($PrimaryHallmarks[0]);
  if (count($SecondaryHallmarks) >= 1 && $SecondaryHallmarks[0] == "All")
    unset($SecondaryHallmarks[0]);
  if (count($ThirdHallmarks) >= 1 && $ThirdHallmarks[0] == "All")
    unset($ThirdHallmarks[0]);
  if (count($AdditionalHallmarks) >= 1 && $AdditionalHallmarks[0] == "All")
    unset($AdditionalHallmarks[0]);

// -------------------------------------------------------------------------------------
// Find the item ids for the chosen materials.
//
$mat_item_id_filter = "";
if (count($MaterialList) > 0) {
   $mat_ids = "'" . implode("','", $MaterialList) . "'";
$sql =<<<EOF
         SELECT DISTINCT A.Item_ID
         FROM tblInventory A, tblInventory_Materials B
         WHERE A.Item_ID  = B.Item_ID
           AND A.Website = "True"
           AND B.MaterialID IN ($mat_ids) 
         ORDER BY A.Item_ID;
EOF;
   $matresult = $db->query($sql);

   $mat_item_ids = array();
   while($row = $matresult->fetchArray(SQLITE3_ASSOC) ) {
       array_push($mat_item_ids, $row['Item_ID']);
   }

   if (count($mat_item_ids) > 0) {
     $mat_item_id_filter = "AND A.Item_ID IN " . "('" . implode("','", $mat_item_ids) . "')";
   }
}

// -------------------------------------------------------------------------------------
// Find the item ids for the chosen primary hallmarks.
//
$primary_hallmark_item_id_filter = "";
if (count($PrimaryHallmarks) > 0) {
   $phm_ids = "'" . implode("','", $PrimaryHallmarks) . "'";
$sql =<<<EOF
         SELECT DISTINCT A.Item_ID
         FROM tblInventory A, tblInventory_Hallmarks B, tblHallmarks C, lkpLevels D
         WHERE A.Item_ID  = B.Item_ID
           AND A.Website = 'True'
           AND B.HallmarkID IN ($phm_ids)
           AND B.HallmarkID = C.HallmarkID
           AND C.Level = D.LevelID
           AND D.LevelID = '1'
         ORDER BY A.Item_ID;
EOF;
   $phresult = $db->query($sql);

   $phm_item_ids = array();
   while($row = $phresult->fetchArray(SQLITE3_ASSOC) ) {
       array_push($phm_item_ids, $row['Item_ID']);
   }

   if (count($phm_item_ids) > 0) {
     $primary_hallmark_item_id_filter = "AND A.Item_ID IN " . "('" . implode("','", $phm_item_ids) . "')";
   }
}

// -------------------------------------------------------------------------------------
// Find the item ids for the chosen secondary hallmarks.
//
$secondary_hallmark_item_id_filter = "";
if (count($SecondaryHallmarks) > 0) {
   $shm_ids = "'" . implode("','", $SecondaryHallmarks) . "'";
$sql =<<<EOF
         SELECT DISTINCT A.Item_ID
         FROM tblInventory A, tblInventory_Hallmarks B, tblHallmarks C, lkpLevels D
         WHERE A.Item_ID  = B.Item_ID
           AND A.Website = 'True'
           AND B.HallmarkID IN ($shm_ids) 
           AND B.HallmarkID = C.HallmarkID
           AND C.Level = D.LevelID
           AND D.LevelID = '2'
         ORDER BY A.Item_ID;
EOF;
   $shresult = $db->query($sql);

   $shm_item_ids = array();
   while($row = $shresult->fetchArray(SQLITE3_ASSOC) ) {
       array_push($shm_item_ids, $row['Item_ID']);
   }

   if (count($shm_item_ids) > 0) {
     $secondary_hallmark_item_id_filter = "AND A.Item_ID IN " . "('" . implode("','", $shm_item_ids) . "')";
   }
}

// -------------------------------------------------------------------------------------
// Find the item ids for the chosen tertiary hallmarks.
//
$third_hallmark_item_id_filter = "";
if (count($ThirdHallmarks) > 0) {
   $thm_ids = "'" . implode("','", $ThirdHallmarks) . "'";
$sql =<<<EOF
         SELECT DISTINCT A.Item_ID
         FROM tblInventory A, tblInventory_Hallmarks B, tblHallmarks C, lkpLevels D
         WHERE A.Item_ID  = B.Item_ID
           AND A.Website = 'True'
           AND B.HallmarkID IN ($thm_ids) 
           AND B.HallmarkID = C.HallmarkID
           AND C.Level = D.LevelID
           AND D.LevelID = '3'
         ORDER BY A.Item_ID;
EOF;
   $thresult = $db->query($sql);

   $thm_item_ids = array();
   while($row = $thresult->fetchArray(SQLITE3_ASSOC) ) {
       array_push($thm_item_ids, $row['Item_ID']);
   }

   if (count($thm_item_ids) > 0) {
     $third_hallmark_item_id_filter = "AND A.Item_ID IN " . "('" . implode("','", $thm_item_ids) . "')";
   }
}

// -------------------------------------------------------------------------------------
// Find the item ids for the chosen additional hallmarks.
//
$additional_hallmark_item_id_filter = "";
if (count($AdditionalHallmarks) > 0) {
   $ahm_ids = "'" . implode("','", $AdditionalHallmarks) . "'";
$sql =<<<EOF
         SELECT DISTINCT A.Item_ID
         FROM tblInventory A, tblInventory_Hallmarks B, tblHallmarks C, lkpLevels D
         WHERE A.Item_ID  = B.Item_ID
           AND A.Website = 'True'
           AND B.HallmarkID IN ($ahm_ids)  
           AND B.HallmarkID = C.HallmarkID
           AND C.Level = D.LevelID
           AND D.LevelID = '4'
         ORDER BY A.Item_ID;
EOF;
   $ahresult = $db->query($sql);

   $ahm_item_ids = array();
   while($row = $ahresult->fetchArray(SQLITE3_ASSOC) ) {
       array_push($ahm_item_ids, $row['Item_ID']);
   }

   if (count($ahm_item_ids) > 0) {
     $additional_hallmark_item_id_filter = "AND A.Item_ID IN " . "('" . implode("','", $ahm_item_ids) . "')";
   }
}

// -------------------------------------------------------------------------------------
// Find the items with chosen item types filtered by chosen materials and hallmarks.
//
if (count($ItemTypes) > 0) {
   $ItemTypesList = "'" . implode("','", $ItemTypes) . "'";
$sql =<<<EOF
      SELECT A.Item_ID, A.Description, A.Identity_Number, B.ItemType
      FROM  tblInventory A LEFT OUTER JOIN lkpItemTypes B ON A.ItemType = B.ItemTypeID
      WHERE A.Website = "True" AND B.ItemTypeID IN ($ItemTypesList) $mat_item_id_filter $primary_hallmark_item_id_filter $secondary_hallmark_item_id_filter $third_hallmark_item_id_filter $additional_hallmark_item_id_filter
      ORDER BY A.CatalogSort, B.ItemType, A.Item_ID;
EOF;
} else {
$sql =<<<EOF
      SELECT A.Item_ID, A.Description, A.Identity_Number, B.ItemType
      FROM  tblInventory A LEFT OUTER JOIN lkpItemTypes B ON A.ItemType = B.ItemTypeID
      WHERE A.Website = "True"  $mat_item_id_filter $primary_hallmark_item_id_filter $secondary_hallmark_item_id_filter $third_hallmark_item_id_filter $additional_hallmark_item_id_filter
      ORDER BY A.CatalogSort, B.ItemType, A.Item_ID;
EOF;      
}
   $dbresult = $db->query($sql);

} // End of item search case.
	 
   // Count results
   $nrows = 0;
   $dbresult->reset();
   while ($dbresult->fetchArray())
      $nrows++;
   $dbresult->reset();

  include "sidebar.htm";
?>

<font size="2" face="arial"><strong>Search Results <?php echo $nrows ?> matches.</strong></font>

<?php if ($nrows > 0): ?>

<font size="2" face="arial"><strong>Click on the Thumbnail or ID Number for more information.</strong></font><br>

<p></p>

<!---- Create table of results ------------------------------------------------->

<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td align="center" valign="bottom" bgcolor="#C0C0C0">
          <font size="2" face="arial"><strong>Thumbnail</strong></font></td>
        <td align="center" valign="bottom" bgcolor="#C0C0C0">
          <font size="2" face="arial"><strong>ID</strong></font></td>
        <td align="center" valign="bottom" bgcolor="#C0C0C0">
          <font size="2" face="arial"><strong>Description</strong></font></td>
    </tr>

<?php
   while($row = $dbresult->fetchArray(SQLITE3_ASSOC) ) {
      echo '<tr><td valign="top"><font size="2" face="arial">';
      $item_id = $row['Item_ID'];
      $description = $row['Description'];
      $imagefile = strtolower($row['Identity_Number']) . '_t.jpg';
      $FileName = "photos/$imagefile";
      if (file_exists($FileName)) {
        echo "<a href=\"item_page.php?id=$item_id\"><img src=\"/photos/$imagefile\" alt=\"Thumbnail\" border=\"0\" onload=trapclick()></a>";
      } else {
        echo '<img src="/photos/nothumb.gif" height="52" width="140" alt="Thumbnail" border="0" onload=trapclick()>';
      }
      echo '</font></td>';
      echo "<td valign=\"top\" align=\"middle\"><font size=\"2\" face=\"arial\"><a href=\"item_page.php?id=$item_id\"><strong>$item_id</a></strong></font></td>";
      echo "<td valign=\"top\"><font size=\"2\" face=\"arial\">$description</font></td>";
      echo '</tr>';
  }

  $db->close();
?>

</table>

<?php endif; ?>

<?php include "sidebar_end.htm"; ?>

</body>
</html>
