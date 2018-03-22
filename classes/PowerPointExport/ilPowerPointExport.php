<?php

namespace LiveVoting\PowerPointExport;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use ilObjLiveVoting;
use ilLiveVotingPlugin;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use ilUtil;
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

		$this->pl = ilLiveVotingPlugin::getInstance();

		$this->temp_folder = $this->getTempFolder();
		$this->temp_file = $this->temp_folder . ".pptx";
		$this->file_name = $this->getFileName();

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
			'voting_type' => xlvoQuestionTypes::getActiveTypes()
		])->orderBy('position', 'ASC')->get());
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

		foreach ($this->votings as $num => $voting) {
			$this->addVoting($voting, ($num + 1));
		}
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function addVoting(xlvoVoting $voting, $num) {
		copy(__DIR__ . "/thumbnail.png", $this->temp_folder . "/ppt/media/image" . $num . ".png");

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

		$xml = new DOMDocument();

		$xml->load($file);

		$tree = $xml->getElementsByTagName("sld")->item(0)->getElementsByTagName("cSld")->item(0)->getElementsByTagName("spTree")->item(0);

		$node = $xml->createDocumentFragment();
		$node->appendXML('<mc:AlternateContent xmlns:mc="http://schemas.openxmlformats.org" xmlns:p="http://schemas.openxmlformats.org" xmlns:a="http://schemas.openxmlformats.org" xmlns:a16="http://schemas.openxmlformats.org" xmlns:r="http://schemas.openxmlformats.org" xmlns:we="http://schemas.openxmlformats.org"><mc:Choice Requires="we pca"><p:graphicFrame><p:nvGraphicFramePr><p:cNvPr id="4" name="Add-In 3" title="Webviewer"><a:extLst><a:ext uri="{FF2B5EF4-FFF2-40B4-BE49-F238E27FC236}"><a16:creationId id="'
			. $guid
			. '"/></a:ext></a:extLst></p:cNvPr><p:cNvGraphicFramePr><a:graphicFrameLocks noGrp="1"/></p:cNvGraphicFramePr><p:nvPr/></p:nvGraphicFramePr><p:xfrm><a:off x="1524000" y="857249"/><a:ext cx="9144000" cy="5143500"/></p:xfrm><a:graphic><a:graphicData uri="http://schemas.microsoft.com/office/webextensions/webextension/2010/11"><we:webextensionref r:id="rId2"/></a:graphicData></a:graphic></p:graphicFrame></mc:Choice><mc:Fallback><p:pic><p:nvPicPr><p:cNvPr id="4" name="Add-In 3" title="Webviewer"><a:extLst><a:ext uri="{FF2B5EF4-FFF2-40B4-BE49-F238E27FC236}"><a16:creationId id="'
			. $guid
			. '"/></a:ext></a:extLst></p:cNvPr><p:cNvPicPr><a:picLocks noGrp="1" noRot="1" noChangeAspect="1" noMove="1" noResize="1" noEditPoints="1" noAdjustHandles="1" noChangeArrowheads="1" noChangeShapeType="1"/></p:cNvPicPr><p:nvPr/></p:nvPicPr><p:blipFill><a:blip r:embed="rId3"/><a:stretch><a:fillRect/></a:stretch></p:blipFill><p:spPr><a:xfrm><a:off x="1524000" y="857249"/><a:ext cx="9144000" cy="5143500"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></p:spPr></p:pic></mc:Fallback></mc:AlternateContent>');
		$tree->appendChild($node);

		$xml->save($file);
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
		$node->appendXML('<Relationship Id="rId2" Type="http://schemas.microsoft.com/office/2011/relationships/webextension" Target="../webextensions/webextension'
			. $num
			. '.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image'
			. $num . '.png"/>');
		$types->appendChild($node);

		$xml->save($file);
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function updateWebExtension(xlvoVoting $voting, $num) {
		$presenter_link = $this->obj->getPresenterLink() . "&voting=" . $voting->getId();
		$presenter_link = "https://google.ch";

		$secure = (stripos($presenter_link, "https://") === 0);
		$link = substr($presenter_link, stripos($presenter_link, "://") + 3);

		$guid = $this->guid();

		$file = $this->temp_folder . "/ppt/webextensions/webextension" . $num . ".xml";

		file_put_contents($file, '<we:webextension id="' . $guid
			. '"><we:reference id="wa104295828" version="1.6.0.0" store="de-CH" storeType="OMEX"/><we:alternateReferences><we:reference id="wa104295828" version="1.6.0.0" store="wa104295828" storeType="OMEX"/></we:alternateReferences><we:properties><we:property name="__labs__" value="{"configuration":{"appVersion":{"major":1,"minor":0},"components":[{"type":"Labs.Components.ActivityComponent","name":"LiveVoting","values":{},"data":{"uri":"'
			. $link . '"},"secure":' . var_export($secure, true)
			. '}],"name":"LiveVoting","timeline":null,"analytics":null},"hostVersion":{"major":0,"minor":1}}"/></we:properties><we:bindings/><we:snapshot r:embed="rId1"/></we:webextension>');
	}


	/**
	 * @param xlvoVoting $voting
	 * @param int        $num
	 */
	protected function updateWebExtensionRel(xlvoVoting $voting, $num) {
		$file = $this->temp_folder . "/ppt/webextensions/_rels/webextension" . $num . ".xml.rels";

		file_put_contents($file, '<Relationships><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image'
			. $num . '.png"/></Relationships>');
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
