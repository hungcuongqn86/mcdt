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


use [ecs-qb-lt-convert]
--select count(*) from T_eCS_RECORD where year(C_INPUT_DATE) = 2016
--select count(*) from T_eCS_RECORD where year(C_INPUT_DATE) = 2017
Delete [ecs-qb-lt-convert].[dbo].T_eCS_RECORD where [FK_RECORDTYPE] not in (select PK_RECORDTYPE from [ecs-qb-lt-convert].[dbo].T_eCS_RECORDTYPE)
Delete [ecs-qb-lt-convert].[dbo].T_eCS_RECORD_WORK where [FK_RECORD] not in (select [PK_RECORD] from [ecs-qb-lt-convert].[dbo].T_eCS_RECORD)
Delete [ecs-qb-lt-convert].[dbo].[T_eCS_RECORD_RELATE_STAFF] where [FK_RECORD] not in (select [PK_RECORD] from [ecs-qb-lt-convert].[dbo].T_eCS_RECORD)
Delete [ecs-qb-lt-convert].[dbo].[T_eCS_RECORD_RELATE_UNIT] where [FK_RECORD] not in (select [PK_RECORD] from [ecs-qb-lt-convert].[dbo].T_eCS_RECORD)
Delete [ecs-qb-lt-convert].[dbo].[T_eCS_REMINDER] where [FK_RECORD] not in (select [PK_RECORD] from [ecs-qb-lt-convert].[dbo].T_eCS_RECORD)

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
	,[C_NEWID_RTA] from [ecs-qb-lt-convert].[dbo].T_eCS_RECORD

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
	,[C_OWNER_CODE] from [ecs-qb-lt-convert].[dbo].T_eCS_RECORD_WORK


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
	,[C_ROLES] from [ecs-qb-lt-convert].[dbo].[T_eCS_RECORD_RELATE_STAFF]


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
	,[C_APPOINTED_DATE] from [ecs-qb-lt-convert].[dbo].[T_eCS_RECORD_RELATE_UNIT]

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
	,[C_LOCATION] from [ecs-qb-lt-convert].[dbo].[T_eCS_REMINDER]
