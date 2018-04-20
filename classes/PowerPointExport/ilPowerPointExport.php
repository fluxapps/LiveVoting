<?php

namespace LiveVoting\PowerPointExport;

use ilLink;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use ilObjLiveVoting;
use ilLiveVotingPlugin;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use ilUtil;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Slide\Note;
use xlvoVotingConfig;
use ZipArchive;
use DOMDocument;

/**
 * http://officeopenxml.com/anatomyofOOXML-pptx.php
 *
 * @package LiveVoting\PowerPointExport
 */
class ilPowerPointExport {

	/**
	 * @var ilObjLiveVoting
	 */
	protected $obj;
	/**
	 * @var xlvoVotingConfig
	 */
	protected $config;
	/**
	 * @var string
	 */
	protected $temp_folder;
	/**
	 * @var string
	 */
	protected $temp_file;
	/**
	 * @var string
	 */
	protected $file_name;
	/**
	 * @var xlvoVoting[]
	 */
	protected $votings;
	/**
	 * @var PhpPresentation
	 */
	protected $pp;
	/**
	 * @var ilLiveVotingPlugin
	 */
	protected $pl;


	/**
	 * @param ilObjLiveVoting $obj
	 */
	public function __construct(ilObjLiveVoting $obj) {
		$this->obj = $obj;
		$this->config = xlvoVotingConfig::find($this->obj->getId());

		$this->pl = ilLiveVotingPlugin::getInstance();

		$this->temp_folder = $this->getTempFolder();
		ilUtil::makeDirParents($this->temp_folder);
		$this->temp_file = $this->temp_folder . ".pptx";
		$this->file_name = $this->getFileName();
	}


	/**
	 *
	 */
	public function run() {
		$this->loadVotings();

		$this->pp = new PhpPresentation();
		$this->pp->removeSlideByIndex(0); // Remove default slide

		$this->setDocumentProperties();

		$this->createSlides();

		$this->createPowerPoint();

		$this->unzip();

		$this->addVotings();

		$this->zip();

		$this->deliver();
	}


	/**
	 *
	 */
	protected function loadVotings() {
		$this->votings = array_values(xlvoVoting::where([
			'obj_id' => $this->obj->getId(),
			'voting_type' => xlvoQuestionTypes::getActiveTypes(),
		])->orderBy('position', 'ASC')->get()); // Correct index with array_values
	}


	/**
	 *
	 */
	protected function setDocumentProperties() {
		$documentProperties = $this->pp->getDocumentProperties();

		$documentProperties->setTitle($this->obj->getTitle());

		$documentProperties->setDescription($this->obj->getDescription());

		$documentProperties->setSubject($this->pl->txt("obj_xlvo"));

		$documentProperties->setCompany(ILIAS_HTTP_PATH);

		$documentProperties->setCreator("ILIAS");

		$documentProperties->setLastModifiedBy("ILIAS");

		$documentProperties->setCreated(strtotime($this->obj->getCreateDate()));

		$documentProperties->setModified(strtotime($this->obj->getLastUpdateDate()));
	}


	/**
	 *
	 */
	protected function createSlides() {
		foreach ($this->votings as $voting) {
			$this->createSlide($voting);
		}
	}


	/**
	 * @param xlvoVoting $voting
	 */
	protected function createSlide(xlvoVoting $voting) {
		$slide = $this->pp->createSlide();

		$slide->setName($voting->getTitle());

		$this->addNotes($voting, $slide);
	}


	/**
	 * @param xlvoVoting $voting
	 * @param Slide      $slide
	 */
	protected function addNotes(xlvoVoting $voting, Slide $slide) {
		$data = [
			"voting_title" => $voting->getTitle(),
			"voting_question" => strip_tags($voting->getQuestion()),
			"empty1" => "",
			"voting_short_link" => $this->config->getShortLinkURL(),
			"voting_permanent_link" => ilLink::_getStaticLink($this->obj->getRefId(), $this->obj->getType()),
			"empty2" => ""
		];

		$note = new Note();
		$note->createRichTextShape()->setWidth(600)->setHeight(300)->createTextRun(implode("\n", array_map(function ($txt, $value) {
			if ($txt !== "" && $value !== "") {
				return $this->pl->txt($txt) . ": " . $value;
			} else {
				// Empty line
				return "";
			}
		}, array_keys($data), $data)));

		$slide->setNote($note);
	}


	/**
	 *
	 */
	protected function createPowerPoint() {
		$writer = IOFactory::createWriter($this->pp, 'PowerPoint2007');

		$writer->save($this->temp_file);
	}


	/**
	 *
	 */
	protected function addVotings() {
		ilUtil::makeDirParents($this->temp_folder . "/ppt/webextensions/_rels");
		ilUtil::makeDirParents($this->temp_folder . "/ppt/media");

		foreach ($this->votings as $i => $voting) {
			$this->addVoting($voting, ($i + 1));
		}
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function addVoting(xlvoVoting $voting, $num) {
		copy(__DIR__ . "/../../templates/images/thumbnail.png", $this->temp_folder . "/ppt/media/image" . $num . ".png");

		$this->updateContentTypes($voting, $num);
		$this->updateSlide($voting, $num);
		$this->updateSlideRel($voting, $num);
		$this->updateWebExtension($voting, $num);
		$this->updateWebExtensionRel($voting, $num);
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function updateContentTypes(xlvoVoting $voting, $num) {
		$file = $this->temp_folder . "/[Content_Types].xml";

		$xml = new DOMDocument();

		$xml->load($file);

		$types = $xml->getElementsByTagName("Types")->item(0);

		$node = $xml->createDocumentFragment();
		$node->appendXML('<Override PartName="/ppt/webextensions/webextension' . $num
			. '.xml" ContentType="application/vnd.ms-office.webextension+xml"/>');
		$types->appendChild($node);

		$xml->save($file);
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function updateSlide(xlvoVoting $voting, $num) {
		$file = $this->temp_folder . "/ppt/slides/slide" . $num . ".xml";

		$guid = $this->guid();
		$rand = rand(1000000000, 9999999999);
		$title = $voting->getTitle();
		$question = strip_tags($voting->getQuestion());

		$xml = file_get_contents($file);

		$pos = stripos($xml, "</p:grpSpPr>");
		$xml = substr($xml, 0, ($pos + 12)) . '
<mc:AlternateContent xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006">
	<mc:Choice xmlns:we="http://schemas.microsoft.com/office/webextensions/webextension/2010/11" xmlns:pca="http://schemas.microsoft.com/office/powerpoint/2013/contentapp" Requires="we pca">
		<p:graphicFrame>
			<p:nvGraphicFramePr>
				<p:cNvPr id="' . ($num * 2 - 1) . '" descr="' . htmlspecialchars($question) . '" name="Add-In ' . $num . '" title="'
			. htmlspecialchars($title) . '">
					<a:extLst>
						<a:ext uri="{FF2B5EF4-FFF2-40B4-BE49-F238E27FC236}">
							<a16:creationId xmlns:a16="http://schemas.microsoft.com/office/drawing/2014/main" id="' . $guid . '"/>
						</a:ext>
					</a:extLst>
				</p:cNvPr>
				<p:cNvGraphicFramePr>
					<a:graphicFrameLocks noGrp="1"/>
				</p:cNvGraphicFramePr>
				<p:nvPr/>
			</p:nvGraphicFramePr>
			<p:xfrm>
				<a:off x="0" y="300000"/>
				<a:ext cx="9144000" cy="5143500"/>
			</p:xfrm>
			<a:graphic>
				<a:graphicData uri="http://schemas.microsoft.com/office/webextensions/webextension/2010/11">
					<we:webextensionref xmlns:we="http://schemas.microsoft.com/office/webextensions/webextension/2010/11" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:id="rId3"/>
				</a:graphicData>
			</a:graphic>
		</p:graphicFrame>
	</mc:Choice>
	<mc:Fallback>
		<p:pic>
			<p:nvPicPr>
				<p:cNvPr id="' . ($num * 2) . '" descr="' . htmlspecialchars($question) . '" name="Add-In ' . $num . '" title="'
			. htmlspecialchars($title) . '">
					<a:extLst>
						<a:ext uri="{FF2B5EF4-FFF2-40B4-BE49-F238E27FC236}">
							<a16:creationId xmlns:a16="http://schemas.microsoft.com/office/drawing/2014/main" id="' . $guid . '"/>
						</a:ext>
					</a:extLst>
				</p:cNvPr>
				<p:cNvPicPr>
					<a:picLocks noGrp="1" noRot="1" noChangeAspect="1" noMove="1" noResize="1" noEditPoints="1" noAdjustHandles="1" noChangeArrowheads="1" noChangeShapeType="1"/>
				</p:cNvPicPr>
				<p:nvPr/>
			</p:nvPicPr>
			<p:blipFill>
				<a:blip r:embed="rId4"/>
				<a:stretch>
					<a:fillRect/>
				</a:stretch>
			</p:blipFill>
			<p:spPr>
				<a:xfrm>
					<a:off x="0" y="300000"/>
					<a:ext cx="9144000" cy="5143500"/>
				</a:xfrm>
				<a:prstGeom prst="rect">
					<a:avLst/>
				</a:prstGeom>
			</p:spPr>
		</p:pic>
	</mc:Fallback>
</mc:AlternateContent>' . substr($xml, ($pos + 12));

		file_put_contents($file, $xml);

		// DOMDocument not works or is buggy because unknown namespace?!
		/**
		 * $xml = new DOMDocument();
		 *
		 * $xml->load($file);
		 *
		 * $tree = $xml->getElementsByTagName("sld")->item(0)->getElementsByTagName("cSld")->item(0)->getElementsByTagName("spTree")->item(0);
		 *
		 * $node = $xml->createDocumentFragment();
		 * $node->appendXML('XML');
		 * $tree->appendChild($node);
		 *
		 * $xml->save($file);
		 */
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function updateSlideRel(xlvoVoting $voting, $num) {
		$file = $this->temp_folder . "/ppt/slides/_rels/slide" . $num . ".xml.rels";

		$xml = new DOMDocument();

		$xml->load($file);

		$types = $xml->getElementsByTagName("Relationships")->item(0);

		$node = $xml->createDocumentFragment();
		$node->appendXML('<Relationship Id="rId3" Type="http://schemas.microsoft.com/office/2011/relationships/webextension" Target="../webextensions/webextension'
			. $num . '.xml"/>
<Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image' . $num . '.png"/>');
		$types->appendChild($node);

		$xml->save($file);
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function updateWebExtension(xlvoVoting $voting, $num) {
		$presenter_link = $this->config->getPresenterLink($voting->getId());

		$secure = (stripos($presenter_link, "https://") === 0);
		$link = substr($presenter_link, (stripos($presenter_link, "://") + 3));

		$guid = $this->guid();

		$file = $this->temp_folder . "/ppt/webextensions/webextension" . $num . ".xml";

		file_put_contents($file, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<we:webextension xmlns:we="http://schemas.microsoft.com/office/webextensions/webextension/2010/11" id="' . $guid . '">
	<we:reference id="wa104295828" version="1.6.0.0" store="de-CH" storeType="OMEX"/>
	<we:alternateReferences>
		<we:reference id="wa104295828" version="1.6.0.0" store="wa104295828" storeType="OMEX"/>
	</we:alternateReferences>
	<we:properties>
		<we:property name="__labs__" value="{&quot;configuration&quot;:{&quot;appVersion&quot;:{&quot;major&quot;:1,&quot;minor&quot;:0},&quot;components&quot;:[{&quot;type&quot;:&quot;Labs.Components.ActivityComponent&quot;,&quot;name&quot;:&quot;'
			. htmlspecialchars($link) . '&quot;,&quot;values&quot;:{},&quot;data&quot;:{&quot;uri&quot;:&quot;' . htmlspecialchars($link)
			. '&quot;},&quot;secure&quot;:' . var_export($secure, true) . '}],&quot;name&quot;:&quot;' . htmlspecialchars($link) . '&quot;,&quot;timeline&quot;:null,&quot;analytics&quot;:null},&quot;hostVersion&quot;:{&quot;major&quot;:0,&quot;minor&quot;:1}}"/>
	</we:properties>
	<we:bindings/>
	<we:snapshot xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:embed="rId1"/>
</we:webextension>');
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function updateWebExtensionRel(xlvoVoting $voting, $num) {
		$file = $this->temp_folder . "/ppt/webextensions/_rels/webextension" . $num . ".xml.rels";

		file_put_contents($file, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
	<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image' . $num . '.png"/>
</Relationships>');
	}


	/**
	 *
	 */
	protected function unzip() {
		// ilUtil::unzip does not create folder

		$zip = new ZipArchive();
		$zip->open($this->temp_file);
		$zip->extractTo($this->temp_folder);
		$zip->close();
	}


	/**
	 *
	 */
	protected function zip() {
		ilUtil::zip($this->temp_folder, $this->temp_file, true);

		ilUtil::delDir($this->temp_folder);
	}


	/**
	 *
	 */
	protected function deliver() {
		ilUtil::deliverFile($this->temp_file, $this->file_name, "", false, true, true);
	}


	/**
	 * @return string
	 */
	protected function getTempFolder() {
		return CLIENT_DATA_DIR . "/temp/" . uniqid(ilLiveVotingPlugin::PLUGIN_ID . "_pp_", true);
	}


	/**
	 * @return string
	 */
	protected function getFileName() {
		return $this->obj->getTitle() . ".pptx";
	}


	/**
	 * Source http://php.net/manual/de/function.com-create-guid.php
	 *
	 * @return string
	 */
	protected function guid() {
		if (function_exists('com_create_guid')) {
			return trim(com_create_guid(), '{}');
		}

		return "{"
			. sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535))
			. "}";
	}
}
