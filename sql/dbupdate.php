<#1>
	<?php
	$fields = array(
		'id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true
		)
	);

	$ilDB->createTable("rep_robj_xlvo_data", $fields);
	$ilDB->addPrimaryKey("rep_robj_xlvo_data", array( "id" ));
	$ilDB->createSequence("rep_robj_xlvo_data");
	?>
	<#2>
		<?php
		require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteOption.php');
		xlvoSingleVoteOption::installDB();
		?>
		<#3>
			<?php
			require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteVote.php');
			xlvoSingleVoteVote::installDB();
			?>
			<#4>
				<?php
				require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteVoting.php');
				xlvoSingleVoteVoting::installDB();
				?>
