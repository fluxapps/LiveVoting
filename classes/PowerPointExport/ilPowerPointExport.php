<?php

namespace LiveVoting\PowerPointExport;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Shape\Hyperlink;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use ilObjLiveVoting;
use ilLiveVotingPlugin;
use LiveVoting\Voting\xlvoVoting;
use LiveVoting\QuestionTypes\xlvoQuestionTypes;
use ilUtil;

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
	protected $temp_file;
	/**
	 * @var string
	 */
	protected $file_name;
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

		$this->temp_file = $this->getTempFile();
		$this->file_name = $this->getFileName();

		$this->pp = new PhpPresentation();
		$this->pp->removeSlideByIndex(0);

		$this->setDocumentProperties();

		$this->addVotingsOfObject();

		$this->createPowerPoint();

		$this->deliver();
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
	protected function addVotingsOfObject() {
		/**
		 * @var xlvoVoting[] $votings
		 */
		$votings = xlvoVoting::where([
			'obj_id' => $this->obj->getId(),
			'voting_type' => xlvoQuestionTypes::getActiveTypes()
		])->orderBy('position', 'ASC')->get();

		foreach ($votings as $voting) {
			$this->addVoting($voting);
		}
	}


	/**
	 * @param xlvoVoting $voting
	 */
	protected function addVoting(xlvoVoting $voting) {
		$slide = $this->pp->createSlide();

		$slide->setName($voting->getTitle());

		$presenter_link = $this->obj->getPresenterLink() . "&voting=" . $voting->getId();
		$shape = $slide->createRichTextShape();

		$shape->setHeight(300)->setWidth(600)->setOffsetX(100)->setOffsetY(100);

		$shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

		$textRun = $shape->createTextRun($presenter_link);
		$textRun->setHyperlink(new Hyperlink($presenter_link));
		$textRun->getFont()->setBold(true)->setSize(30)->setColor(new Color('FFE06B20'));
		/**
		 * <p:graphicFrame><p:nvGraphicFramePr><p:cNvPr id="4" name="Add-In 3" title="Webviewer"><a:extLst><a:ext uri="{FF2B5EF4-FFF2-40B4-BE49-F238E27FC236}"><a16:creationId id="{DAA66948-CCB0-4C02-A2B4-185808A78F8A}"/></a:ext></a:extLst></p:cNvPr><p:cNvGraphicFramePr><a:graphicFrameLocks noGrp="1"/></p:cNvGraphicFramePr><p:nvPr/></p:nvGraphicFramePr><p:xfrm><a:off x="1524000" y="857249"/><a:ext cx="9144000" cy="5143500"/></p:xfrm><a:graphic><a:graphicData uri="http://schemas.microsoft.com/office/webextensions/webextension/2010/11"><we:webextensionref r:id="rId2"/></a:graphicData></a:graphic></p:graphicFrame></mc:Choice>
		 */
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
	protected function deliver() {
		ilUtil::deliverFile($this->temp_file, $this->file_name, "", false, true, true);
	}


	/**
	 * @return string
	 */
	protected function getTempFile() {
		return CLIENT_DATA_DIR . "/temp/" . uniqid(ilLiveVotingPlugin::PLUGIN_ID . "_pp_", true) . ".pptx";
	}


	/**
	 * @return string
	 */
	protected function getFileName() {
		return $this->obj->getTitle() . ".pptx";
	}
}
