<?php
/**
 * Purchase Capital Request System
 *
 * labels.php variables for english.
 *
 * @version 1.5
 * @link https//hr.Company.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 */

/**
 * - Set display and color status for approval levels
 */
function showContent($approval) {
	switch ($approval) {
		case 'app1':
			$display['stage3']['display']='display';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='none';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='none';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='none';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='none';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';										
		break;
		case 'app2':
			$display['stage3']['display']='display';
				$display['stage3']['accent_border']='BGAccentDarkBlueBorder2';
				$display['stage3']['accent']='BGAccentDarkBlue';
				$display['stage3']['accent_text']='white';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBlueBorder2';
				$display['stage1.3']['accent']='BGAccentDarkBlue';
				$display['stage1.3']['accent_text']='white';			
			$display['actualcompensation']['display']='none';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='none';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';
				$display['employeeinformation']['accent_text']='black';			
			$display['stage1.4']['display']='display';
				$display['stage1.4']['accent_border']='BGAccentDarkBlueBorder2';
				$display['stage1.4']['accent']='BGAccentDarkBlue';		
				$display['stage1.4']['accent_text']='white';	
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';						
		break;
		case 'app3':
			$display['stage3']['display']='display';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBlueBorder2';
				$display['stage1.3']['accent']='BGAccentDarkBlue';		
				$display['stage1.3']['accent_text']='white';			
			$display['actualcompensation']['display']='none';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='none';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='display';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';	
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';												
		break;		
		case 'app4':
			$display['stage3']['display']='display';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='none';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='none';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='display';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';	
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';									
		break;
		case 'app5':
			$display['stage3']['display']='none';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='none';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='none';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='display';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';							
		break;
		case 'app6':
			$display['stage3']['display']='none';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='display';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='display';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='display';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';	
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';								
		break;
		case 'app7':
			$display['stage3']['display']='none';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='display';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='none';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='none';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';	
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';					
		break;				
		case 'app8':
			$display['stage3']['display']='none';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='display';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='none';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='none';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';						
		break;
		case 'staffing':
			$display['stage3']['display']='display';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='display';
				$display['actualcompensation']['accent_border']='BGAccentDarkBlueBorder2';
				$display['actualcompensation']['accent']='BGAccentDarkBlue';
				$display['actualcompensation']['accent_text']='white';			
			$display['employeeinformation']['display']='display';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBlueBorder2';
				$display['employeeinformation']['accent']='BGAccentDarkBlue';
				$display['employeeinformation']['accent_text']='white';			
			$display['stage1.4']['display']='display';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBlueBorder';
				$display['stage5']['accent']='BGAccentDarkBlue';
				$display['stage5']['accent_text']='white';					
		break;
		case 'coordinator':
			$display['stage3']['display']='none';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='none';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='none';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='display';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='none';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';
		break;	
		default:
			$display['stage3']['display']='display';
				$display['stage3']['accent_border']='BGAccentDarkBorder';
				$display['stage3']['accent']='BGAccentDark';		
				$display['stage3']['accent_text']='black';
			$display['stage1.3']['display']='display';
				$display['stage1.3']['accent_border']='BGAccentDarkBorder';
				$display['stage1.3']['accent']='BGAccentDark';		
				$display['stage1.3']['accent_text']='black';			
			$display['actualcompensation']['display']='display';
				$display['actualcompensation']['accent_border']='BGAccentDarkBorder';
				$display['actualcompensation']['accent']='BGAccentDark';		
				$display['actualcompensation']['accent_text']='black';			
			$display['employeeinformation']['display']='display';	
				$display['employeeinformation']['accent_border']='BGAccentDarkBorder';
				$display['employeeinformation']['accent']='BGAccentDark';		
				$display['employeeinformation']['accent_text']='black';		
			$display['stage1.4']['display']='display';
				$display['stage1.4']['accent_border']='BGAccentDarkBorder';
				$display['stage1.4']['accent']='BGAccentDark';		
				$display['stage1.4']['accent_text']='black';	
			$display['stage5']['display']='display';
				$display['stage5']['accent_border']='BGAccentDarkBorder';
				$display['stage5']['accent']='BGAccentDark';
				$display['stage5']['accent_text']='white';							
		break;	
	}
	
	return $display;
}



/**
 * - Set display and color status for approval levels
 */
/*switch ($_GET['approval']) {
	case 'app2':
		$inputStatus['positionTitle'] = 'class="hideinput" readonly';
		$inputStatus['plant'] = 'class="hideinput" readonly';
		$inputStatus['department'] = 'class="hideinput" readonly';
		$inputStatus['positionStatus'] = 'class="hideinput" readonly';
		$inputStatus['replacement'] = 'class="hideinput" readonly';
		$inputStatus['positionType'] = 'class="hideinput" readonly';
		$inputStatus['requestType'] = 'class="hideinput" readonly';
		$inputStatus['contractTime'] = 'class="hideinput" readonly';
		$inputStatus['replacement'] = 'class="hideinput" readonly';
		$inputStatus['startDate'] = 'class="hideinput" readonly';
		$inputStatus['budgetPosition'] = "Budget Position";
		$inputStatus['justification'] = "Justification";
		$inputStatus['utilize'] = "Utilize Other Staff";
		$inputStatus['headCount'] = "Budgeted Head Count";
		$inputStatus['currentHeadCount'] = "Current Head Count";
		$inputStatus['budget'] = "Budget";
		$inputStatus['salaryGrade'] = "Salary Grade";
		$inputStatus['salaryType'] = "Salary Type";
		$inputStatus['salary'] = "Salary";
		$inputStatus['overTime'] = "Over Time";
		$inputStatus['doubleTime'] = "Double Time";
		$inputStatus['vehicleAllowance'] = "Vehicle Allowance";
		$inputStatus['vactionDays'] = "Vaction Days";
		$inputStatus['teamMember'] = "Interview Team Member";
		
		$inputStatus['file'] = "Attachment";
		$inputStatus['description'] = "Description";
		$inputStatus['primaryJob'] = "Primary Responsibilities";
		$inputStatus['secondaryJob'] = "Secondary Responsibilities";
	break;
}*/
?>