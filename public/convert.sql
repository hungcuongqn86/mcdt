--Clier db
/*
use [ecs-qb-lt]
delete T_eCS_NET_RECORD
delete T_eCS_NET_USER where C_ROLES is null
delete T_eCS_RECORD
delete T_eCS_RECORD_RELATE_STAFF
delete T_eCS_RECORD_RELATE_STAFF_OF_UNIT
delete T_eCS_RECORD_RELATE_TRANSITION
delete T_eCS_RECORD_RELATE_UNIT
delete T_eCS_RECORD_TAX_AND_TREASURY
delete T_eCS_RECORD_TRANSITION
delete T_eCS_RECORD_TRANSITION_RELATE_STAFF
delete T_eCS_RECORD_TRANSITION_RELATE_UNIT
delete T_eCS_RECORD_WORK
delete T_eCS_REMINDER
delete T_eCS_SMS_RECEIVED
delete T_eCS_SMS_SEND
delete T_eCS_SMS_USER
*/


use [efy-ecs]
--select count(*) from T_eCS_RECORD where year(C_INPUT_DATE) = 2016
--select count(*) from T_eCS_RECORD where year(C_INPUT_DATE) = 2017
Delete [efy-ecs].[dbo].T_eCS_RECORD where [FK_RECORDTYPE] not in (select PK_RECORDTYPE from [efy-ecs].[dbo].T_eCS_RECORDTYPE)
Delete [efy-ecs].[dbo].T_eCS_RECORD_WORK where [FK_RECORD] not in (select [PK_RECORD] from [efy-ecs].[dbo].T_eCS_RECORD)
Delete [efy-ecs].[dbo].[T_eCS_RECORD_RELATE_STAFF] where [FK_RECORD] not in (select [PK_RECORD] from [efy-ecs].[dbo].T_eCS_RECORD)
Delete [efy-ecs].[dbo].[T_eCS_RECORD_RELATE_UNIT] where [FK_RECORD] not in (select [PK_RECORD] from [efy-ecs].[dbo].T_eCS_RECORD)
Delete [efy-ecs].[dbo].[T_eCS_REMINDER] where [FK_RECORD] not in (select [PK_RECORD] from [efy-ecs].[dbo].T_eCS_RECORD)

use [ecs-qb-lt]

Insert into [ecs-qb-lt].[dbo].T_eCS_RECORD (
	[PK_RECORD]
	,[FK_RECORDTYPE]
	,[FK_RECEIVER]
	,[C_RECEIVER_POSITION_NAME]
	,[C_INPUT_DATE]
	,[C_RECEIVED_DATE]
	,[C_APPOINTED_DATE]
	,[C_HAVE_TO_RESULT_DATE]
	,[C_RESULT_DATE]
	,[C_CURRENT_STATUS]
	,[C_DETAIL_STATUS]
	,[C_RECEIVED_RECORD_XML_DATA]
	,[C_LICENSE_XML_DATA]
	,[C_RESULT_RECEIVER]
	,[C_COST]
	,[C_REASON]
	,[C_OWNER_CODE]
	,[C_CODE]
	,[C_SUBMIT_ORDER_DATE]
	,[C_SUBMIT_ORDER_CONTENT]
	,[C_FILE_NAME]
	,[C_TAX_APPOINTED_DATE]
	,[C_TREASURY_APPOINTED_DATE]
	,[C_NEWID_RTA]
)

select [PK_RECORD]
	,[FK_RECORDTYPE]
	,[FK_RECEIVER]
	,[C_RECEIVER_POSITION_NAME]
	,[C_INPUT_DATE]
	,[C_RECEIVED_DATE]
	,[C_APPOINTED_DATE]
	,[C_HAVE_TO_RESULT_DATE]
	,[C_RESULT_DATE]
	,[C_CURRENT_STATUS]
	,[C_DETAIL_STATUS]
	,convert(nvarchar(max),[C_RECEIVED_RECORD_XML_DATA]) as [C_RECEIVED_RECORD_XML_DATA]
	,convert(nvarchar(max),[C_LICENSE_XML_DATA]) as [C_LICENSE_XML_DATA]
	,[C_RESULT_RECEIVER]
	,[C_COST]
	,[C_REASON]
	,[C_OWNER_CODE]
	,[C_CODE]
	,[C_SUBMIT_ORDER_DATE]
	,[C_SUBMIT_ORDER_CONTENT]
	,[C_FILE_NAME]
	,[C_TAX_APPOINTED_DATE]
	,[C_TREASURY_APPOINTED_DATE]
	,[C_NEWID_RTA] from [efy-ecs].[dbo].T_eCS_RECORD

-- select * from [ecs-qb-lt].[dbo].T_eCS_RECORD

Insert into [ecs-qb-lt].[dbo].T_eCS_RECORD_WORK (
	[PK_RECORD_WORK]
	,[FK_RECORD]
	,[C_WORK_DATE]
	,[C_WORKTYPE]
	,[C_RESULT]
	,[FK_STAFF]
	,[C_POSITION_NAME]
	,[C_SYSTEM_WORKTYPE]
	,[C_OWNER_CODE]
)

select [PK_RECORD_WORK]
	,[FK_RECORD]
	,[C_WORK_DATE]
	,[C_WORKTYPE]
	,[C_RESULT]
	,[FK_STAFF]
	,[C_POSITION_NAME]
	,[C_SYSTEM_WORKTYPE]
	,[C_OWNER_CODE] from [efy-ecs].[dbo].T_eCS_RECORD_WORK


Insert into [ecs-qb-lt].[dbo].[T_eCS_RECORD_RELATE_STAFF] (
	[PK_RECORD_RELATE_STAFF]
	,[FK_RECORD]
	,[FK_STAFF]
	,[C_POSITION_NAME]
	,[C_ROLES]
)
select [PK_RECORD_RELATE_STAFF]
	,[FK_RECORD]
	,[FK_STAFF]
	,[C_POSITION_NAME]
	,[C_ROLES] from [efy-ecs].[dbo].[T_eCS_RECORD_RELATE_STAFF]


Insert into [ecs-qb-lt].[dbo].[T_eCS_RECORD_RELATE_UNIT] (
	[PK_RECORD_RELATE_UNIT]
	,[FK_RECORD]
	,[FK_UNIT]
	,[C_UNIT_NAME]
	,[C_ASSIGNED_DATE]
	,[C_ASSIGNED_IDEA]
	,[C_APPOINTED_DATE]
)
select [PK_RECORD_RELATE_UNIT]
	,[FK_RECORD]
	,[FK_UNIT]
	,[C_UNIT_NAME]
	,[C_ASSIGNED_DATE]
	,[C_ASSIGNED_IDEA]
	,[C_APPOINTED_DATE] from [efy-ecs].[dbo].[T_eCS_RECORD_RELATE_UNIT]

Insert into [ecs-qb-lt].[dbo].[T_eCS_REMINDER] (
	[PK_REMINDER]
	,[FK_RECORD]
	,[FK_RECORDTYPE]
	,[C_CURRENT_STATUS]
	,[C_DETAIL_STATUS]
	,[C_OWNER_CODE]
	,[C_APPOINTED_DATE]
	,[C_LOCATION]
)
select [PK_REMINDER]
	,[FK_RECORD]
	,[FK_RECORDTYPE]
	,[C_CURRENT_STATUS]
	,[C_DETAIL_STATUS]
	,[C_OWNER_CODE]
	,[C_APPOINTED_DATE]
	,[C_LOCATION] from [efy-ecs].[dbo].[T_eCS_REMINDER]

----------------------------------
-----------USER-------------------

select * from [ecs-user-qb].dbo.T_USER_STAFF order by PK_STAFF
select * from [efy-user].dbo.T_USER_STAFF
where PK_STAFF not in (select PK_STAFF from [ecs-user-qb].dbo.T_USER_STAFF)

-- update [ecs-user-qb].dbo.T_USER_STAFF set PK_STAFF = '0931CEC8-F360-4340-9057-14F29B6D09B5' where PK_STAFF = 'B8BB88EF-D0B2-4859-885E-D82470E881C3'
-- update [ecs-user-qb].dbo.T_USER_STAFF set PK_STAFF = 'C4115B2A-223C-4FD0-A89C-983DA38623C5' where PK_STAFF = '34DEDE52-7AF2-4DE5-9801-20D4F8BD8FBB'

update [ecs-user-qb].dbo.T_USER_STAFF 
set C_PASSWORD = (select top 1 C_PASSWORD from [efy-user].dbo.T_USER_STAFF where [ecs-user-qb].dbo.T_USER_STAFF.PK_STAFF = [efy-user].dbo.T_USER_STAFF.PK_STAFF)
where PK_STAFF in (select PK_STAFF from [efy-user].dbo.T_USER_STAFF)