---- 0301

---- 0302
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = '9EFC8832-75F0-4E57-A871-AC2BEEDF6D61' and c_code <> 'HLT.0302.17.00624'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dt_nguoi_chuyen')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				set @addxml = '[{&#34;ben_nhan_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'nguoi_nhan')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dia_chi_nguoi_nhan')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dt_nguoi_nhan')
				set @addxml = @addxml + '&#34;}]'
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxmladd,'ben_nhan_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

--- 0303
if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = 'BEC24B6A-E4CD-4517-BE7B-78E2C763F516' and c_code <> 'HLT.0303.17.00394'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0304

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = 'D12F8D29-41F6-4FC6-8238-01C56DE6BEB4' and c_code <> 'HLT.0304.17.00065'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0305

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = '62672933-130D-495C-B2E0-B1E442A0FE79' and c_code <> 'HLT.0305.17.00906'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dt_nguoi_chuyen')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				set @addxml = '[{&#34;ben_nhan_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'nguoi_nhan')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dia_chi_nguoi_nhan')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dt_nguoi_nhan')
				set @addxml = @addxml + '&#34;}]'
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxmladd,'ben_nhan_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0306

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = 'E0CA8638-FB5C-40C0-9003-372B4EE6CD53' and c_code <> 'HLT.0306.17.00527'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dt_nguoi_chuyen')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				set @addxml = '[{&#34;ben_nhan_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'nguoi_nhan')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dia_chi_nguoi_nhan')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dt_nguoi_nhan')
				set @addxml = @addxml + '&#34;}]'
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxmladd,'ben_nhan_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0307

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = '5F2EBA24-003A-4579-AD22-91E1FC86239B' and c_code <> 'HLT.0307.17.00023'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0311

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = '9784FC73-D569-49E8-9CED-8759300AFC8E' and c_code <> 'HLT.0311.17.00356'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0322

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = 'A15AD3FA-A8E4-41D2-AB33-FD2F8B3C22EB' and c_code <> 'HLT.0322.17.00021'
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_nhan_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_name')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'registor_address')
				set @addxml = @addxml + '&#34;,&#34;ben_nhan_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk')
				set @addxml = @addxml + '&#34;}]'
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_nhan_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0323

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = '7A0811C4-7EDA-45F7-8945-937624329454' and C_XML_OLD is not null
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'registor_name'), '' )
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'registor_address'), '' )
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk'), '' )
				set @addxml = @addxml + '&#34;}]'
				-- print @record_id
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

----0324

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = '99B12AFF-84E1-4B6F-9225-340696B3A59B' and C_XML_OLD is not null
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'registor_name'), '' )
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'registor_address'), '' )
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk'), '' )
				set @addxml = @addxml + '&#34;}]'
				-- print @record_id
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO

---0328

if exists (select * from dbo.sysobjects where id = object_id(N'[dbo].[convertxml]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
drop procedure [dbo].[convertxml]
GO
CREATE PROCEDURE [dbo].[convertxml]
AS
	Declare @record_id varchar(50),@strxml varchar(max),@addxml varchar(max),@strxmladd varchar(max)
	SET NOCOUNT ON
	BEGIN TRANSACTION
		Create Table #T_RECORD_LIST(PK_RECORD varchar(50))
		insert into #T_RECORD_LIST (PK_RECORD) select PK_RECORD from T_eCS_RECORD where FK_RECORDTYPE = '4FA0AAB1-F48B-4E00-8711-14BBDE8FBC1C' and C_XML_OLD is not null
		-- select * from #T_RECORD_LIST
		set @strxml = ''
		set @addxml = ''
		set @strxmladd = ''
		DECLARE new_record CURSOR For Select PK_RECORD From #T_RECORD_LIST
		Open new_record
		FETCH NEXT FROM new_record INTO @record_id
		WHILE @@FETCH_STATUS = 0
			BEGIN
				select @strxml = C_XML_OLD from T_eCS_RECORD where PK_RECORD = @record_id
				set @addxml = '[{&#34;ben_chuyen_nhuong_obj_name_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'registor_name'), '' )
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_address_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'registor_address'), '' )
				set @addxml = @addxml + '&#34;,&#34;ben_chuyen_nhuong_obj_phone_0&#34;:&#34;'
				set @addxml = @addxml + ISNULL(dbo.f_GetValueOfXMLtag(@strxml,'dien_thoai_nk'), '' )
				set @addxml = @addxml + '&#34;}]'
				-- print @record_id
				-- print convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))
				set @strxmladd = convert(nvarchar(max),dbo.f_AddXMLtag(@strxml,'ben_chuyen_nhuong',@addxml))

				update T_eCS_RECORD set C_RECEIVED_RECORD_XML_DATA = @strxmladd where PK_RECORD = @record_id
				FETCH NEXT FROM new_record INTO @record_id
			END
		CLOSE new_record
		DEALLOCATE new_record
	COMMIT TRANSACTION 
	SET NOCOUNT OFF
Return 0
GO


---0329
