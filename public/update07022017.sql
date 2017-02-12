USE [ecs-qb-lt]
GO
/****** Object:  StoredProcedure [dbo].[eCS_RecordGetAll]    Script Date: 02/07/2017 00:56:30 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER PROCEDURE [dbo].[eCS_RecordGetAll]
	@sRecordTypeId					nvarchar(50) -- Id loai danh muc ho so TTHC
	,@sRecordType					Varchar(30)  -- Loai ho so TTHC: LIEN_THONG, KHONG_LIEN_THONG
	,@iCurrentStaffId				Varchar(50)	 -- Id can bo dang nhap hien thoi
	,@sReceiveDate					Datetime	 -- Ngay tiep nhan
	,@sStatusList					Varchar(1000)-- Danh sach trang thai giai quyet ho so
	,@sDetailStatusCompare			Varchar(500) -- Chuoi dieu kien lay du lieu theo tung truong hop cu the
	,@sRole							Varchar(50)	 -- Nhom quyen NSD
	,@sOrderClause					Varchar(300) -- Chuoi mo ta menh de sap xep du lieu
	,@sOwnerCode					Varchar(20)	 -- Ma don vi su dung
	,@sfulltextsearch				Nvarchar(200)-- Tu, cum tu tim kiem
	,@iPage							Int			 -- Trang hien thoi
	,@iNumberRecordPerPage			Int			 -- So ban ghi tren trang
AS
	Declare @v_str_sql nvarchar(4000), @pTotalRecord int,@dApoidDate datetime
	SET NOCOUNT ON
	Create Table #T_STATUS_LIST(PK_STATUS_LIST Varchar(100))
	Exec Sp_ListToTable @sStatusList, 'PK_STATUS_LIST', '#T_STATUS_LIST', ','	
	-- Tao bang temp
	Create Table #T_ALL_RECORD( PK_RECORD Varchar(50)
								,C_CODE Varchar(30)
								,FK_RECORDTYPE Varchar(50)
								,C_RECEIVED_DATE Datetime
								,C_APPOINTED_DATE Datetime
								,C_RECEIVED_RECORD_XML_DATA xml
								,C_REASON nvarchar(200)
								,C_OWNER_CODE varchar(20)
								,C_SUBMIT_ORDER_DATE	Datetime
								,C_SUBMIT_ORDER_CONTENT nvarchar(200)
								,C_FILE_NAME nvarchar(300)
								,PK_RECORD_TRANSITION Varchar(50)
								,C_CURRENT_STATUS Varchar(50)
								,C_DETAIL_STATUS int
								,C_TAX_APPOINTED_DATE datetime
								,C_TREASURY_APPOINTED_DATE datetime
	)
	Set @v_str_sql = ' Insert into #T_ALL_RECORD '
	Set @v_str_sql = @v_str_sql + ' Select PK_RECORD,C_CODE,FK_RECORDTYPE,C_RECEIVED_DATE,C_APPOINTED_DATE
										   ,C_RECEIVED_RECORD_XML_DATA,C_REASON,C_OWNER_CODE,C_SUBMIT_ORDER_DATE,C_SUBMIT_ORDER_CONTENT,C_FILE_NAME,'+char(39)+char(39)+',C_CURRENT_STATUS,C_DETAIL_STATUS,C_TAX_APPOINTED_DATE,C_TREASURY_APPOINTED_DATE'
	Set @v_str_sql = @v_str_sql + ' From T_eCS_RECORD A Where 1=1 '
	-- Loc theo cac tieu chi tim kiem
	If @sRecordTypeId<>'' And @sRecordTypeId Is Not Null
		Set @v_str_sql = @v_str_sql + ' And FK_RECORDTYPE = ' + char(39) + @sRecordTypeId + char(39)	 
	If @sReceiveDate Is Not Null And @sReceiveDate <> ''
		Set @v_str_sql = @v_str_sql + ' And datediff(d,C_RECEIVED_DATE,' + char(39)+ Convert(varchar,@sReceiveDate)+ char(39)+')=0'	
	If @sStatusList Is Not Null And @sStatusList <> ''
		Set @v_str_sql = @v_str_sql + ' And (C_CURRENT_STATUS In (Select PK_STATUS_LIST From #T_STATUS_LIST)) '
	If @sOwnerCode Is Not Null And @sOwnerCode <> ''
		Set @v_str_sql = @v_str_sql + ' And C_OWNER_CODE = ' + char(39) + @sOwnerCode + char(39)
	If @sRole <> '' And @sRole is not null
		Begin
			Set @v_str_sql = @v_str_sql + 
			case @sRole
				When @sRole Then ' And PK_RECORD In (Select FK_RECORD From T_eCS_RECORD_RELATE_STAFF Where C_ROLES = ' + CHAR(39) + @sRole + CHAR(39) +' And FK_STAFF = ' + char(39)+ @iCurrentStaffId+ char(39) + ')'
			End
		End
	If @sfulltextsearch <> '' And @sfulltextsearch is not null
		Set @v_str_sql = @v_str_sql + ' And convert(nvarchar(max),C_DATA_TEMP.query(' + char(39) + '/root/data_list/*/text()' + char(39) + ')) like' + char(39) + '%' + dbo.Lower2Upper(@sfulltextsearch) + '%' + char(39)
	-- Chuoi mo ta dieu kien
	If @sDetailStatusCompare <> '' And @sDetailStatusCompare Is Not Null 
		Set @v_str_sql = @v_str_sql + ' ' + @sDetailStatusCompare
	If @sOrderClause<>'' And @sOrderClause Is Not Null 
		Set @v_str_sql = @v_str_sql + ' ' + @sOrderClause
	-- PRINT @v_str_sql
	Exec (@v_str_sql)
    -- Temp table contain sorted data
	Create Table #T_ALL_SORTED_RECORD(  P_ID int IDENTITY (1,1)
										,PK_RECORD Varchar(50)
										,C_CODE Varchar(30)
										,FK_RECORDTYPE Varchar(50)
										,C_RECEIVED_DATE Datetime
										,C_APPOINTED_DATE Datetime
										,C_RECEIVED_RECORD_XML_DATA xml
										,C_REASON nvarchar(200)
										,C_OWNER_CODE varchar(20)
										,C_SUBMIT_ORDER_DATE	Datetime
										,C_SUBMIT_ORDER_CONTENT nvarchar(200)
										,C_FILE_NAME nvarchar(300)
										,PK_RECORD_TRANSITION Varchar(50)
										,C_CURRENT_STATUS varchar(50)
										,C_DETAIL_STATUS int
										,C_TAX_APPOINTED_DATE datetime
										,C_TREASURY_APPOINTED_DATE datetime
	)
	Insert Into #T_ALL_SORTED_RECORD
	Select * From #T_ALL_RECORD
	Order By C_RECEIVED_DATE  Desc

	Select @pTotalRecord = count(*)  From #T_ALL_SORTED_RECORD
	Select PK_RECORD
			,C_CODE
			,FK_RECORDTYPE
			,convert(varchar(10),C_RECEIVED_DATE,103) + ' ' + convert(varchar(5),C_RECEIVED_DATE,108) As C_RECEIVED_DATE
			,convert(varchar(10),C_APPOINTED_DATE,103) As C_APPOINTED_DATE
			,C_RECEIVED_RECORD_XML_DATA
			,C_REASON
			,C_OWNER_CODE
			,PK_RECORD_TRANSITION
			,@pTotalRecord as C_TOTAL_RECORD 
			,case @sRole
				when 'THULY_CHINH' then 
						dbo.f_GetOutofDateofRecord(getdate(),dbo.f_GetAppointedDate(PK_RECORD,@iCurrentStaffId))		
				else		
						case C_CURRENT_STATUS when 'CHUYEN_TIEP' 
							then case C_DETAIL_STATUS when '22' then dbo.f_GetOutofDateofRecord(getdate(),C_TAX_APPOINTED_DATE)
														when '23' then dbo.f_GetOutofDateofRecord(getdate(),C_TREASURY_APPOINTED_DATE)
														end
							else dbo.f_GetOutofDateofRecord(getdate(),C_APPOINTED_DATE) 
							end 
				end As OUT_OF_DATE
			,C_SUBMIT_ORDER_DATE
			,C_SUBMIT_ORDER_CONTENT
			,C_FILE_NAME
			,C_CURRENT_STATUS
			,C_DETAIL_STATUS
			,dbo.f_GetTextStatus(C_CURRENT_STATUS,C_DETAIL_STATUS) As C_TEXT_STATUS
			,case C_CURRENT_STATUS when 'TRA_LAI' then '<font color="#FF0000">'+C_SUBMIT_ORDER_CONTENT+'</font>'
									when 'THU_LY' then dbo.f_GetAssignedIdea(PK_RECORD,@iCurrentStaffId,@sOwnerCode)+' ('+convert(varchar(10),dbo.f_GetAssignedDate(PK_RECORD,@iCurrentStaffId,@sOwnerCode),103)+')'
									when 'CHUYEN_TIEP' then 
														case (select C_CURRENT_STATUS from T_eCS_RECORD_TRANSITION where FK_RECORD=#T_ALL_SORTED_RECORD.PK_RECORD)
															when 'THU_LY' then dbo.f_GetAssignedIdea(PK_RECORD,@iCurrentStaffId,@sOwnerCode)+' ('+convert(varchar(10),dbo.f_GetAssignedDate(PK_RECORD,@iCurrentStaffId,@sOwnerCode),103)+')'
															when 'TRA_LAI' then '<font color="#FF0000">'+(select C_SUBMIT_ORDER_CONTENT from T_eCS_RECORD_TRANSITION where FK_RECORD=#T_ALL_SORTED_RECORD.PK_RECORD)+'</font>'
															else C_SUBMIT_ORDER_CONTENT
														end
									when 'BO_SUNG' then (select C_RESULT from T_eCS_RECORD_WORK where FK_RECORD=#T_ALL_SORTED_RECORD.PK_RECORD and C_WORKTYPE='YEU_CAU_BO_SUNG' and C_WORK_DATE= (select Max(C_WORK_DATE)from T_eCS_RECORD_WORK where FK_RECORD=#T_ALL_SORTED_RECORD.PK_RECORD and C_WORKTYPE='YEU_CAU_BO_SUNG'))
									else C_SUBMIT_ORDER_CONTENT end as C_MESSAGE
			,(select top 1 'ÄÃ£ xÃ¡c nháº­n' from T_eCS_RECORD_WORK where FK_RECORD=#T_ALL_SORTED_RECORD.PK_RECORD and C_WORKTYPE='XAC_NHAN_DU_THONG_TIN') as C_CONFIRM_STATUS
			From #T_ALL_SORTED_RECORD
 			Where  P_ID >((@iPage - 1) * @iNumberRecordPerPage) and P_ID <= (@iPage * @iNumberRecordPerPage)
	SET NOCOUNT OFF
Return 0


---------------------------------------
USE [ecs-qb-lt]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER PROCEDURE [dbo].[eCS_RecordTypeWardConfigUpdate]
	@sRecordTypePk						Varchar(50)
	,@sStaffId							Varchar(50)
	,@sWardOwnerCode					varchar(max)
 
AS
	SET NOCOUNT ON
	BEGIN TRANSACTION 
		Update T_eCS_RECORDTYPE_RELATE_STAFF set C_WARD_CODE = @sWardOwnerCode where FK_RECORDTYPE = @sRecordTypePk and FK_STAFF = @sStaffId and C_ROLES = 'TIEP_NHAN' 
	COMMIT TRANSACTION 
		Select * from T_eCS_RECORDTYPE_RELATE_STAFF where FK_RECORDTYPE = @sRecordTypePk and FK_STAFF = @sStaffId and C_ROLES = 'TIEP_NHAN'
	SET NOCOUNT OFF
Return 0

----------------------------
USE [ecs-qb-lt]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
Alter PROCEDURE [dbo].[eCS_RecordTypeGetWardConfig]
	@sRecordTypePk 			Varchar(50)
	,@sStaffId				Varchar(50)
As
	SET NOCOUNT ON
		Select	*
		 From	T_eCS_RECORDTYPE_RELATE_STAFF
		 Where  FK_RECORDTYPE = @sRecordTypePk And FK_STAFF = @sStaffId and C_ROLES = 'TIEP_NHAN'
	SET NOCOUNT OFF
Return 0
