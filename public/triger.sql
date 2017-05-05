USE [ecs-qb-lt]
GO
SET ANSI_NULLS, QUOTED_IDENTIFIER ON
GO
Create Trigger eCS_TriggerRecordUpdate
On T_eCS_RECORD
WITH ENCRYPTION
For Update
As
	Declare @record varchar(50),@recordtype varchar(50),@currentstatus varchar(50),@detailstatus smallint,@ownercode varchar(50),@appointeddate datetime
	--Lay cac thong tin ve ho so duoc update
	Select @record=PK_RECORD,@recordtype=FK_RECORDTYPE,@currentstatus=C_CURRENT_STATUS,@detailstatus=C_DETAIL_STATUS,@ownercode=C_OWNER_CODE,@appointeddate=C_APPOINTED_DATE
	From Inserted
	--Xoa ban ghi tuong ung tren bang T_eCS_REMINDER 
	Delete T_eCS_REMINDER Where FK_RECORD=@record And FK_RECORDTYPE=@recordtype
	--Kiem tra neu khong phai ho so da tra ket qua hoac tu choi nhan ho so lien thong qua mang thi cap nhat lai bang T_eCS_REMINDER
	if(@detailstatus<>51 AND @detailstatus<>241)
		Begin
			Insert into T_eCS_REMINDER(PK_REMINDER,FK_RECORD,FK_RECORDTYPE,C_CURRENT_STATUS,C_DETAIL_STATUS,C_OWNER_CODE,C_APPOINTED_DATE,C_LOCATION)
					Values(newid(),@record,@recordtype,@currentstatus,@detailstatus,@ownercode,@appointeddate,1)
		End
	else --xoa thong tin phan cong doi voi ho so het luu thong 
		begin
			Delete T_eCS_RECORD_RELATE_STAFF Where FK_RECORD=@record
		end
GO


USE [ecs-qb-lt]
GO
SET ANSI_NULLS, QUOTED_IDENTIFIER ON
GO
Create Trigger eCS_TriggerRecordInsert
On T_eCS_RECORD
WITH ENCRYPTION
For Insert
As
	Declare @record varchar(50),@recordtype varchar(50),@currentstatus varchar(50),@detailstatus smallint,@ownercode varchar(50),@appointeddate datetime
	--Lay cac thong tin ve ho so duoc update
	Select @record=PK_RECORD,@recordtype=FK_RECORDTYPE,@currentstatus=C_CURRENT_STATUS,@detailstatus=C_DETAIL_STATUS,@ownercode=C_OWNER_CODE,@appointeddate=C_APPOINTED_DATE
	From Inserted
	--cap nhat thong tin vao bang T_eCS_REMINDER
	Insert into T_eCS_REMINDER(PK_REMINDER,FK_RECORD,FK_RECORDTYPE,C_CURRENT_STATUS,C_DETAIL_STATUS,C_OWNER_CODE,C_APPOINTED_DATE,C_LOCATION)
			Values(newid(),@record,@recordtype,@currentstatus,@detailstatus,@ownercode,@appointeddate,1)
GO



USE [ecs-qb-lt]
GO
SET ANSI_NULLS, QUOTED_IDENTIFIER ON
GO
Create Trigger eCS_TriggerRecordDelete
On T_eCS_RECORD
WITH ENCRYPTION
For Delete
As
	Declare @record varchar(50),@recordtype varchar(50)
	--Lay cac thong tin ve ho so duoc update
	Select @record=PK_RECORD,@recordtype=FK_RECORDTYPE
	From Deleted
	--Xoa ban ghi tuong ung tren bang T_eCS_REMINDER 
	Delete T_eCS_REMINDER Where FK_RECORD=@record And FK_RECORDTYPE=@recordtype	
	Delete T_eCS_RECORD_RELATE_STAFF Where FK_RECORD=@record
GO
