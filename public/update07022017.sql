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
	 PRINT @v_str_sql
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

------------------------------
ALTER PROCEDURE [dbo].[eCS_GetAllReminder]
	@sUserID	Varchar(50) 			--ID nguoi dung
	,@sUnitID	Varchar(50) 			--ID nguoi dung
	,@sOwncode	Varchar(50) 			--Ma don vi su dung
AS
	SET NOCOUNT ON	
		--tao bang tam de luu thong tin
		Create Table #T_STATUS_LIST(PK_RECORDTYPE varchar(50),C_NAME Nvarchar(300), C_RECORD_NET_PRELIMINARY int,C_NEW_RECORD int,C_TRANSITION_RECORD int,C_ADDITIONAL_RECORD int,C_RECORD_RESULT int,C_REFUSER int,C_SOLVING_HANLDER int,C_SOLVING_HANLDER_OVER int,C_RECORD_LEADER_RETURN int,C_RECORD_APPROVAL_RETURN int,C_RECORD_ASSIGNMENT int,C_RECORD_APPROVAL int,C_RECORD_APPROVAL_OVER int)
		--cap nhat vao bang tam
		Insert into #T_STATUS_LIST
		Select A.PK_RECORDTYPE
		-- Lay ten loai ho so
		,(Select C_NAME From T_eCS_RECORDTYPE Where PK_RECORDTYPE = A.PK_RECORDTYPE) AS C_NAME
		--Ho so cho nhan so bo qua mang
		,(Select count(*) From T_eCS_REMINDER  T Where 'TIEP_NHAN' in (select value from dbo.f_getRelateStaff (@sUserID,A.PK_RECORDTYPE)) and (T.C_CURRENT_STATUS = 'CHO_TIEP_NHAN_SO_BO' or T.C_CURRENT_STATUS ='DA_BO_XUNG_CHO_NHAN_SO_BO') and T.C_OWNER_CODE =@sOwncode and T.FK_RECORDTYPE=A.PK_RECORDTYPE And T.C_LOCATION=0) AS C_RECORD_NET_PRELIMINARY


		--Ho so moi tiep nhan
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD B Where T.FK_RECORDTYPE=A.PK_RECORDTYPE And T.C_OWNER_CODE =@sOwncode And T.FK_RECORD=B.PK_RECORD And T.C_DETAIL_STATUS = 10 And T.C_CURRENT_STATUS = 'MOI_TIEP_NHAN' And T.C_LOCATION=1 and @sUserID in (select FK_STAFF from T_eCS_RECORDTYPE_RELATE_STAFF where T_eCS_RECORDTYPE_RELATE_STAFF.FK_RECORDTYPE=T.FK_RECORDTYPE and T_eCS_RECORDTYPE_RELATE_STAFF.C_ROLES='TIEP_NHAN')) AS C_NEW_RECORD



		--Ho so lien thong cho nhan
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD B 
			Where T.FK_RECORDTYPE=A.PK_RECORDTYPE And T.C_OWNER_CODE =@sOwncode And T.FK_RECORD=B.PK_RECORD And T.C_DETAIL_STATUS = 10 And T.C_CURRENT_STATUS = 'LIEN_THONG_CHO_NHAN' And T.C_LOCATION=1 
				and charindex(B.C_WARD_OWNER_CODE, (select top 1 C_WARD_CODE from T_eCS_RECORDTYPE_RELATE_STAFF where T_eCS_RECORDTYPE_RELATE_STAFF.FK_RECORDTYPE=T.FK_RECORDTYPE and T_eCS_RECORDTYPE_RELATE_STAFF.FK_STAFF = @sUserID and T_eCS_RECORDTYPE_RELATE_STAFF.C_ROLES='TIEP_NHAN')) > 0
		) AS C_TRANSITION_RECORD




		-- Ho so cho bo sung
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD B Where T.FK_RECORDTYPE=A.PK_RECORDTYPE And T.C_OWNER_CODE =@sOwncode And T.FK_RECORD=B.PK_RECORD And T.C_DETAIL_STATUS <> 120 And T.C_CURRENT_STATUS = 'BO_SUNG' And T.C_LOCATION=1 and @sUserID in (select FK_STAFF from T_eCS_RECORDTYPE_RELATE_STAFF where T_eCS_RECORDTYPE_RELATE_STAFF.FK_RECORDTYPE=T.FK_RECORDTYPE and T_eCS_RECORDTYPE_RELATE_STAFF.C_ROLES='TIEP_NHAN')) AS C_ADDITIONAL_RECORD
		--,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD_WORK B Where B.FK_RECORD=T.FK_RECORD And T.FK_RECORDTYPE=A.PK_RECORDTYPE And T.C_OWNER_CODE =@sOwncode And T.C_DETAIL_STATUS = 10 And T.C_CURRENT_STATUS = 'BO_SUNG' And T.C_LOCATION=1 And B.FK_STAFF=@sUserID And B.C_WORKTYPE='TIEP_NHAN') AS C_ADDITIONAL_RECORD
		-- Ho so cho tra ket qua
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD B Where T.FK_RECORDTYPE=A.PK_RECORDTYPE And T.C_OWNER_CODE =@sOwncode And T.FK_RECORD=B.PK_RECORD And T.C_DETAIL_STATUS = 41 And T.C_CURRENT_STATUS = 'CAP_PHEP' And T.C_LOCATION=1 and @sUserID in (select FK_STAFF from T_eCS_RECORDTYPE_RELATE_STAFF where T_eCS_RECORDTYPE_RELATE_STAFF.FK_RECORDTYPE=T.FK_RECORDTYPE and T_eCS_RECORDTYPE_RELATE_STAFF.C_ROLES='TIEP_NHAN')) AS C_RECORD_RESULT
		-- Ho so bi tu choi
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD B Where T.FK_RECORDTYPE=A.PK_RECORDTYPE And T.C_OWNER_CODE =@sOwncode And T.FK_RECORD=B.PK_RECORD And T.C_DETAIL_STATUS = 41 And T.C_CURRENT_STATUS = 'TU_CHOI' And T.C_LOCATION=1 and @sUserID in (select FK_STAFF from T_eCS_RECORDTYPE_RELATE_STAFF where T_eCS_RECORDTYPE_RELATE_STAFF.FK_RECORDTYPE=T.FK_RECORDTYPE and T_eCS_RECORDTYPE_RELATE_STAFF.C_ROLES='TIEP_NHAN')) AS C_REFUSER


		-- Ho so cho xu ly cua can bo thu ly chinh
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD_RELATE_STAFF B Where T.FK_RECORD=B.FK_RECORD And B.FK_STAFF=@sUserID And B.[C_STATUS] = 'DANG_XL' and B.C_ROLES='THULY_CHINH' And  (T.C_CURRENT_STATUS= 'THU_LY' Or T.C_CURRENT_STATUS= 'TRA_LAI') and T.C_DETAIL_STATUS='21' and T.FK_RECORDTYPE = A.PK_RECORDTYPE And T.C_LOCATION=1) AS C_SOLVING_HANLDER
		-- Ho so cho xu ly cua can bo thu ly chinh bi qua han
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD_RELATE_STAFF B Where T.FK_RECORD=B.FK_RECORD And B.FK_STAFF=@sUserID And B.[C_STATUS] = 'DANG_XL' and B.C_ROLES='THULY_CHINH' And  (T.C_CURRENT_STATUS= 'THU_LY' Or T.C_CURRENT_STATUS= 'TRA_LAI') and T.C_DETAIL_STATUS='21' and T.FK_RECORDTYPE = A.PK_RECORDTYPE And DATEDIFF(d,T.C_APPOINTED_DATE,getdate())>0 And T.C_LOCATION=1) AS C_SOLVING_HANLDER_OVER
		-- Ho so lanh dao tra lai
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD_RELATE_STAFF B Where T.FK_RECORD=B.FK_RECORD And B.FK_STAFF=@sUserID And B.[C_STATUS] = 'DANG_XL' and T.C_CURRENT_STATUS= 'TRA_LAI' and T.C_DETAIL_STATUS='21' and T.FK_RECORDTYPE = A.PK_RECORDTYPE And T.C_LOCATION=1) AS C_RECORD_LEADER_RETURN
		-- Ho so lanh dao duyet chuyen lai
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD_RELATE_STAFF B Where T.FK_RECORD=B.FK_RECORD And B.FK_STAFF=@sUserID And B.[C_STATUS] = 'DANG_XL' and T.C_CURRENT_STATUS= 'CAP_PHEP' and T.C_DETAIL_STATUS='21' and T.FK_RECORDTYPE = A.PK_RECORDTYPE And T.C_LOCATION=1) AS C_RECORD_APPROVAL_RETURN



		-- Ho so cho phan cong
		,(Select count(*) From T_eCS_REMINDER T Where (
		T.C_CURRENT_STATUS= 'CHO_PHAN_CONG' and T.C_DETAIL_STATUS='21' 
		And ('DUYET_CAP_MOT' in (select value from dbo.f_getRelateStaff (@sUserID,A.PK_RECORDTYPE)) Or 'DUYET_CAP_HAI' in (select value from dbo.f_getRelateStaff (@sUserID,A.PK_RECORDTYPE))) 
		And @sUnitID in (select FK_UNIT from T_eCS_RECORD_RELATE_UNIT where FK_RECORD = T.FK_RECORD)
		and T.FK_RECORDTYPE = A.PK_RECORDTYPE And T.C_LOCATION=1)) AS C_RECORD_ASSIGNMENT

		-- Ho so cho phe duyet
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD_RELATE_STAFF B Where T.FK_RECORD=B.FK_RECORD And B.FK_STAFF=@sUserID And B.[C_STATUS] = 'DANG_XL' And T.C_CURRENT_STATUS= 'TRINH_KY' and T.FK_RECORDTYPE = A.PK_RECORDTYPE And T.C_LOCATION=1) AS C_RECORD_APPROVAL
		-- Ho so cho phe duyet bi qua han
		,(Select count(*) From T_eCS_REMINDER T,T_eCS_RECORD_RELATE_STAFF B Where T.FK_RECORD=B.FK_RECORD And B.FK_STAFF=@sUserID And B.[C_STATUS] = 'DANG_XL' And T.C_CURRENT_STATUS= 'TRINH_KY' and T.FK_RECORDTYPE = A.PK_RECORDTYPE And T.C_LOCATION=1 And DATEDIFF(d,T.C_APPOINTED_DATE,getdate())>0) AS C_RECORD_APPROVAL_OVER

		From T_eCS_RECORDTYPE A where @sOwncode in (select part from dbo.SplitString(A.C_OWNER_CODE,',')) order by C_ORDER
		--lay thong tin tu bang tam ra (chi lay nhung loai ho so co nhac viec)
		select * from #T_STATUS_LIST where (C_RECORD_NET_PRELIMINARY + C_NEW_RECORD + C_TRANSITION_RECORD + C_ADDITIONAL_RECORD + C_RECORD_RESULT + C_REFUSER + C_SOLVING_HANLDER + C_SOLVING_HANLDER_OVER + C_RECORD_LEADER_RETURN + C_RECORD_APPROVAL_RETURN + C_RECORD_ASSIGNMENT + C_RECORD_APPROVAL + C_RECORD_APPROVAL_OVER)>0
	SET NOCOUNT OFF
Return 0

------------------------------------
------------------------------------
CREATE PROCEDURE dbo.eCS_NetOrderUpdate
	@sNet_RecordID					Varchar(50)			--Ma cua ban ghi (la rong voi them moi, ma cu doi voi sua)
	,@sRecordTypeID					Varchar(50)			--Loai cua ho so
	,@sNetUserID					Varchar(50)			--Ma cua nguoi dang nhap
	,@sCode							Varchar(30)			--Ma ho so
	,@sInput_Date					Varchar(30)			--Ngay gui ho so
	,@dPreliminary_Date				Varchar(30)			--Ngay tiep nhan so bo
	,@dOriginal_Application_Date	Varchar(30)			--Ngay Hen mang ho so goc
	,@dReceiving_Date				Varchar(30)			--Ngay nhan so so chinh thuc
	,@sXml_Data						xml					--Xml luu thong tin khai bao ve thu tuc
	,@sStatus						Varchar(20)			--trang thai TIEP_NHAN_SO_BO,TIEP_NHAN_CHINH_THUC,DA_TIEP_NHAN,BO_XUNG,TU_CHOI,DA_TRA_KQ
	,@sMessage						Varchar(300)		--Thong bao
	,@sUnit							varchar(50)			--don vi nhan ho so qua mang
WITH ENCRYPTION
AS
	Declare @sNewRecordId	Varchar(50),@sgetdate datetime,@sXmltemp nvarchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Set @sXmltemp = dbo.Lower2Upper(convert(nvarchar(max),@sXml_Data))
--		Truong hop chinh sua thong tin mot ho so 
		If @sNet_RecordID is not null and @sNet_RecordID <> ''
			Begin
				Update 	T_eCS_NET_ORDER
				Set		FK_RECORDTYPE						=@sRecordTypeID
						,FK_NET_ID							=@sNetUserID
						,C_CODE								=@sCode
						,C_INPUT_DATE						=@sInput_Date
						,C_PRELIMINARY_DATE					=@dPreliminary_Date
						,C_ORIGINAL_APPLICATION_DATE		=@dOriginal_Application_Date
						,C_RECEIVING_DATE					=@dReceiving_Date
						,C_XML_DATA							=@sXml_Data
						,C_DATA_TEMP						=@sXmltemp
						,C_STATUS							=@sStatus
						,C_MESSAGE							=@sMessage			
						,C_UNIT								=@sUnit
				Where PK_NET_ORDER = @sNet_RecordID
				Set @sNewRecordId = @sNet_RecordID
			End	
		Else
			Begin
				Set @sNewRecordId = NEWID()
				Insert Into 
				T_eCS_NET_ORDER(PK_NET_ORDER, FK_RECORDTYPE, FK_NET_ID, C_CODE, C_INPUT_DATE, C_PRELIMINARY_DATE, C_ORIGINAL_APPLICATION_DATE
							, C_RECEIVING_DATE, C_XML_DATA,C_DATA_TEMP, C_STATUS, C_MESSAGE,C_UNIT							
				)values(	@sNewRecordId, @sRecordTypeID, @sNetUserID, @sCode, getdate(), @dPreliminary_Date
							, @dOriginal_Application_Date, @dReceiving_Date, @sXml_Data,@sXmltemp, @sStatus, @sMessage,@sUnit
				)						
			End		
		Select @sNewRecordId as NEW_ID
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
	Return 0
GO
-----------------------------------------------
------------------------------------------------

Alter PROCEDURE dbo.eCS_NetOrderGetAllByType
	@sRecordTypeID				varchar(50)				--Ma loai danh muc ho so TTHC
	,@sNetUserID				varchar(50)				--Ma cua nguoi dang nhap
	,@currentPage				Int					-- Trang hiện thời
	,@numberRecordPerPage		Int					-- Số bản ghi trên trang
WITH ENCRYPTION
AS
	SET NOCOUNT ON
		Declare @sSql nvarchar(4000)
			,@iTotalRecord int
			,@Paramlist Nvarchar(4000)

		Create Table #T_ALL_RECORD(P_ID int IDENTITY (1,1), PK_NET_ORDER Varchar(50))
		Set @sSql = ' Insert into #T_ALL_RECORD(PK_NET_ORDER)'
		Set @sSql = @sSql + ' Select PK_NET_ORDER'
		Set @sSql = @sSql + ' From T_eCS_NET_ORDER Where 1=1'

		If @sRecordTypeID Is Not Null And @sRecordTypeID <> ''
			Set @sSql = @sSql + ' And FK_RECORDTYPE=@sRecordTypeID1'
		
		If @sNetUserID = '' Set @sNetUserID = null
		Set @sSql = @sSql + ' And FK_NET_ID=@sNetUserID1'
		
		Set @sSql = @sSql+' Order by C_INPUT_DATE desc'
		
		Set @Paramlist ='@sRecordTypeID1				Varchar(50)
						,@sNetUserID1					Varchar(50)'
		Exec SP_EXECUTESQL @sSql
							,@Paramlist
							,@sRecordTypeID
							,@sNetUserID
		
		-- Dem tong so ban ghi
		Select @iTotalRecord = count(*)  From #T_ALL_RECORD
		Delete From #T_ALL_RECORD
		Where	P_ID <=((@currentPage-1) * @numberRecordPerPage)
				or P_ID > @currentPage * @numberRecordPerPage

		--Thuc hien lay DL
		Select	B.PK_NET_ORDER
				,B.FK_RECORDTYPE
				,B.FK_NET_ID
				,B.C_CODE
				,B.C_INPUT_DATE
				,B.C_PRELIMINARY_DATE
				,B.C_ORIGINAL_APPLICATION_DATE
				,B.C_RECEIVING_DATE
				,B.C_XML_DATA
				,B.C_STATUS
				,B.C_MESSAGE
				,B.C_UNIT
				,dbo.f_GetListNamebyCode('DM_CV',C_STATUS) as STATUS_TEX
				,@iTotalRecord As C_TOTAL_RECORD
		From #T_ALL_RECORD A
		Inner Join T_eCS_NET_ORDER B On A.PK_NET_ORDER = B.PK_NET_ORDER
		Order By B.C_INPUT_DATE DESC

	SET NOCOUNT OFF
	Return 0
GO
---------------------------------------------
---------------------------------------------
CREATE PROCEDURE dbo.eCS_NetOrderGetSingle
	@sNetRecordID				varchar(50)		--Danh sach ma ho so can lay thong tin chi tiet
WITH ENCRYPTION
AS
	SET NOCOUNT ON
		Select 	PK_NET_ORDER,FK_RECORDTYPE,FK_NET_ID,C_CODE,C_INPUT_DATE,C_PRELIMINARY_DATE,C_ORIGINAL_APPLICATION_DATE,
		C_RECEIVING_DATE,C_XML_DATA,C_DATA_TEMP,C_STATUS,C_MESSAGE,C_UNIT
		from T_eCS_NET_ORDER
		Where PK_NET_ORDER =@sNetRecordID
		Order by C_INPUT_DATE desc
	SET NOCOUNT OFF
	Return 0
GO
--------------------------------------------------
--------------------------------------------------
ALTER PROCEDURE [dbo].[eCS_NetOrderGetAll]
	 @sRecordTypeID					varchar(50)		--Mã loại hồ sơ
	,@sUnit							varchar(50)		-- Mã phòng ban
	,@sFullTextSearch				varchar(50)		--Tim kiem ho so theo ten nguoi dang ky.
	,@sRecordStatus					varchar(300)	--Trạng thái hồ sơ
	,@iPage							Int				-- Trang hien thoi
	,@iNumberRecordPerPage			Int				-- So ban ghi tren trang
As 
	Set Nocount On
	Declare @v_sql Nvarchar(3000)
	Create Table #T_RECORD(P_ID int IDENTITY (1,1),PK_NET_ORDER Varchar(50))
	set @v_sql='Insert Into #T_RECORD(PK_NET_ORDER) select PK_NET_ORDER from T_eCS_NET_ORDER where 1=1 '
	If @sRecordTypeID Is Not Null And @sRecordTypeID <>''
		Begin 
			Set @v_sql=@v_sql+ ' and FK_RECORDTYPE='+ char(39)+ @sRecordTypeID + char(39)
		End
	-- Tìm theo ho ten nguoi dang ky hay ho ten nguoi khai--
	If @sFullTextSearch Is Not Null And @sFullTextSearch <>''
		Begin 
			Set @v_sql=@v_sql + ' and convert(nvarchar(max),C_DATA_TEMP.query(' + char(39) + '/root/data_list/*/text()' + char(39) + ')) like' + char(39) + '%' + dbo.Lower2Upper(@sFullTextSearch) + '%' + char(39)
		End
	-- Tim theo trang thai ho so --
	If @sRecordStatus Is Not Null And @sRecordStatus <>'' 
		Begin 
			Set @v_sql=@v_sql+ ' and C_STATUS in (select part from SplitString('+char(39)+ @sRecordStatus +char(39)+','+ char(39) +','+ char(39) +'))'
		End
	If @sUnit Is Not Null And @sUnit <>'' 
		Begin 
			Set @v_sql=@v_sql+ ' and C_UNIT='+ char(39)+ @sUnit + char(39)
		End
	Set @v_sql=@v_sql+ ' order by C_INPUT_DATE'
	print (@v_sql)
	exec(@v_sql)
	Select T_eCS_NET_ORDER.PK_NET_ORDER,FK_RECORDTYPE,FK_NET_ID,C_CODE,convert(varchar(30),C_INPUT_DATE,103) as C_INPUT_DATE,C_PRELIMINARY_DATE,C_ORIGINAL_APPLICATION_DATE,
		C_RECEIVING_DATE,C_XML_DATA,C_STATUS,C_MESSAGE
					From  #T_RECORD inner join T_eCS_NET_ORDER on dbo.T_eCS_NET_ORDER.PK_NET_ORDER=#T_RECORD.PK_NET_ORDER
					Where P_ID >((@iPage - 1) * @iNumberRecordPerPage) and P_ID <= (@iPage * @iNumberRecordPerPage)
	
	Set Nocount Off
Return 0
----------------------------------------
------------------------------------------
Alter PROCEDURE [dbo].[eCS_NetOrderApproveUpdate]
	@sNetRecordID					Varchar(50)			--Ma cua ban ghi 
	,@dReceivingDate				Varchar(50)			--Ngay tiep nhan chinh thuc
	,@sStatus						Varchar(50)			--trang thai TIEP_NHAN_SO_BO,TIEP_NHAN_CHINH_THUC,DA_TIEP_NHAN,BO_XUNG,TU_CHOI,DA_TRA_KQ
	,@sMessage						Varchar(300)		--Thong bao
--WITH ENCRYPTION
AS
	Declare @sStrsql varchar(3000)
			,@sRecordType Varchar(50)
			,@sRecordCode Varchar(50)
			,@sXmlData Xml
			,@sOwnerCode Varchar(30)
			,@newid Varchar(50)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		set @sStrsql='Update 	T_eCS_NET_ORDER Set C_MESSAGE ='+char(39)+ @sMessage+char(39)
		if(@dReceivingDate is not null and @dReceivingDate<>'')
			begin
				set @sStrsql=@sStrsql+' ,C_RECEIVING_DATE ='+char(39)+@dReceivingDate+char(39)
			end
		if(@sStatus is not null and @sStatus<>'')
			begin
				set @sStrsql=@sStrsql+' ,C_STATUS ='+char(39)+@sStatus+char(39)
			end
		set @sStrsql=@sStrsql+' where PK_NET_ORDER ='+char(39)+ @sNetRecordID+char(39)
		exec(@sStrsql)
		Select @sNetRecordID As NEW_ID	
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
	Return 0

---------------------------------------------------14/03/2017

ALTER PROCEDURE [dbo].[eCS_RecordUpdate]
	@sRecordId						Varchar(50)			-- ID ho so
	,@sCode							Varchar(30)			-- Ma ho so
	,@sRecordTypeId					Varchar(50)			-- Id loai ho so
	,@iReceiverId					Varchar(50)			-- Id can bo tiep nhan
	,@sReceiverPositionName			Nvarchar(200)		-- Ten, chuc vu can bo tiep nhan
	,@dReceivedDate					DateTime			-- Ngay tiep nhan ho so
	,@dAppointedDate				DateTime			-- Ngay hen tra ket qua
	,@sCurentStatus					Varchar(50)			-- Trang thai hien tai
	,@sDetailStatus					Smallint			-- Trang thai chi tiet
	,@sReceivedRecordXmlData		Xml					-- Xau xml mau don tiep nhan
	,@sLicenseXmlData				Xml					-- Xau xml mau giay phep
	,@sOwnerCode					Varchar(15)			-- Id don vi xu dung
	,@sNewAttachFileNameList		Nvarchar(1000)		-- Danh sach file dinh kem
	,@dWardAppointedDate			DateTime = ''		-- Ngay hen chuyen huyen
AS
	Declare @sNewRecordId	Varchar(50),@sTemp nvarchar(max),@sgetdate datetime,@sXmlTemp Xml,@iRecordTransition int,@iRecordAdd int 
	-- Them cac the vao sau @sXmlTemp phuc vu tra cuu
	Set @sXmlTemp = dbo.f_AddXMLtag(@sReceivedRecordXmlData, 'c_code', @sCode)
	Set @sXmlTemp = dbo.f_AddXMLtag(@sXmlTemp, 'c_receive_date', convert(varchar(10),@dReceivedDate,103))
	Set @sXmlTemp = dbo.f_AddXMLtag(@sXmlTemp, 'c_appointed_date', convert(varchar(10),@dAppointedDate,103))
	if(@sXmlTemp is not null)
		Set @sTemp = dbo.Lower2Upper(convert(nvarchar(max),@sXmlTemp))	
	SET NOCOUNT ON
	BEGIN TRANSACTION					
				--Truong hop chinh sua thong tin mot ho so 
				If @sRecordId is not null and @sRecordId <> ''
					Begin
						select @iRecordAdd=count(*) from T_eCS_RECORD where PK_RECORD=@sRecordId and C_CURRENT_STATUS='BO_SUNG'
						Update 	T_eCS_RECORD
						Set		C_APPOINTED_DATE           = @dAppointedDate
								,C_RECEIVED_RECORD_XML_DATA	= @sReceivedRecordXmlData
								,C_LICENSE_XML_DATA			= @sLicenseXmlData
								,C_DATA_TEMP				= @sTemp
						Where PK_RECORD = @sRecordId
						Set @sNewRecordId = @sRecordId
						if @iRecordAdd>0
							begin
								Set @sgetdate = getdate()
								Exec eCS_RecordWorkSystemUpdate	'',@sNewRecordId,@iReceiverId,@sReceiverPositionName,'BO_SUNG','Bá»• sung há»“ sÆ¡',1,@sgetdate,@sOwnerCode
							end
					end
				Else
					Begin
						Set @sNewRecordId = NEWID()
						Insert Into 
						T_eCS_RECORD(PK_RECORD, C_CODE, FK_RECORDTYPE, FK_RECEIVER, C_RECEIVER_POSITION_NAME, C_INPUT_DATE, C_RECEIVED_DATE
									, C_CURRENT_STATUS, C_DETAIL_STATUS, C_RECEIVED_RECORD_XML_DATA, C_LICENSE_XML_DATA
									, C_OWNER_CODE,C_DATA_TEMP,C_APPOINTED_DATE,C_WARD_END_DATE
						)values(	@sNewRecordId, @sCode, @sRecordTypeId, @iReceiverId, @sReceiverPositionName, getdate(), @dReceivedDate
									, @sCurentStatus, @sDetailStatus, @sReceivedRecordXmlData, @sLicenseXmlData, @sOwnerCode,@sTemp,@dAppointedDate,@dWardAppointedDate
						)		

						--- Cap nhat tien do cong viec
						Set @sgetdate = getdate()
						Exec dbo.eCS_RecordWorkSystemUpdate '',@sNewRecordId,@iReceiverId,@sReceiverPositionName,'TIEP_NHAN','Tiáº¿p nháº­n há»“ sÆ¡',1,@sgetdate,@sOwnerCode
					End
				If @sNewAttachFileNameList is not null and @sNewAttachFileNameList <> '' 
					Begin
						Exec [dbo].[Doc_AttachFileUpdate] @sNewRecordId ,'T_eCS_RECORD', 'HO_SO', @sNewAttachFileNameList, '!#~$|*'
					End
				--Select @sNewRecordId as NEW_ID	
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
		select 
			@sNewRecordId as NEW_ID	
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'loai_hs') As LOAI_HO_SO
			,C_DETAIL_STATUS
			,C_CODE
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'hinh_thuc_nop_hs') As HINH_THUC_NOP_HS
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'registor_name') As CHU_HS
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'registor_address') As DC_CHU_HS
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'registor_phone') As SDT_CHU_HS
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'ten_nguoi_nop_hs') As TEN_NUGOI_NOP
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'ngay_sinh_nguoi_nop_hs') As TUOI_NGUOI_NOP
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'dan_toc_nguoi_nop_hs') As DT_NGUOI_NOP
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'gioi_tinh_nguoi_nop_hs') As SEX_NGUOI_NOP
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'dien_thoai_nguoi_nop_hs') As PHONE_NGUOI_NOP
			,(select C_NAME from T_eCS_RECORDTYPE where T_eCS_RECORDTYPE.PK_RECORDTYPE = T_eCS_RECORD.FK_RECORDTYPE) as TEN_TTHC
			,(select C_NAME from T_EFYLIB_LIST where T_EFYLIB_LIST.C_CODE = (select C_CATE from T_eCS_RECORDTYPE where T_eCS_RECORDTYPE.PK_RECORDTYPE = T_eCS_RECORD.FK_RECORDTYPE)) as LV_TTHC
			,(select CONVERT(varchar, C_RECEIVED_DATE, 105)) As C_RECEIVED_DATE
			,(select CONVERT(varchar, C_APPOINTED_DATE, 105)) As C_APPOINTED_DATE 
			,dbo.f_GetValueOfXMLtag(C_RECEIVED_RECORD_XML_DATA, 'ghi_chu') As GHI_CHU
			,C_NEWID_RTA
		from T_eCS_RECORD where PK_RECORD = @sNewRecordId
Return 0

------------------------------------------------------------

ALTER PROCEDURE [dbo].[eCS_RecordGetSingle]
	@sRecordId		Nvarchar(50) -- Id ho so
	,@sOwnerCode	Varchar(20)	 -- Ma don vi su dung
	,@sRecordTransitionId	Nvarchar(50) = null -- Id ho so lien thong
AS
	SET NOCOUNT ON
		-- Lay thong tin ho so khong lien thong
		Select	convert(varchar(50),PK_RECORD) as PK_RECORD
				,convert(varchar(50),FK_RECORDTYPE) as FK_RECORDTYPE
				,FK_RECEIVER
				,C_RECEIVER_POSITION_NAME
				,convert(varchar(10),C_INPUT_DATE,103) + convert(varchar(5),C_INPUT_DATE,108) As C_INPUT_DATE
				,convert(varchar(10),C_RECEIVED_DATE,103) + convert(varchar(5),C_RECEIVED_DATE,108) As C_RECEIVED_DATE
				,C_RECEIVED_DATE as C_RECEIVED_DATE_EDIT
				,C_APPOINTED_DATE
				,C_WARD_END_DATE
				,C_HAVE_TO_RESULT_DATE
				,C_RESULT_DATE
				,C_CURRENT_STATUS
				,C_DETAIL_STATUS
				,C_RECEIVED_RECORD_XML_DATA
				,C_LICENSE_XML_DATA
				,C_RESULT_RECEIVER
				,C_COST
				,C_REASON
				,C_OWNER_CODE
				,C_CODE
				,C_SUBMIT_ORDER_DATE
				,C_SUBMIT_ORDER_CONTENT
				,C_FILE_NAME
				,(select C_CODE from T_eCS_RECORDTYPE where PK_RECORDTYPE=FK_RECORDTYPE) as C_KEY
		From T_eCS_RECORD 
		Where PK_RECORD = @sRecordId And C_OWNER_CODE = @sOwnerCode
	SET NOCOUNT OFF
Return 0
