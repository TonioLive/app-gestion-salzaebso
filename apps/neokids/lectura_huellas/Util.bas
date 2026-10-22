Attribute VB_Name = "Util"
'-------------------------------------------------------------------------------
'GrFinger Sample
'(c) 2005 Griaule Tecnologia Ltda.
'http://www.griaule.com
'-------------------------------------------------------------------------------
'
'This sample is provided with "GrFinger Fingerprint Recognition Library" and
'can't run without it. It's provided just as an example of using GrFinger
'Fingerprint Recognition Library and should not be used as basis for any
'commercial product.
'
'Griaule Tecnologia makes no representations concerning either the merchantability
'of this software or the suitability of this sample for any particular purpose.
'
'THIS SAMPLE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR
'IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
'OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
'IN NO EVENT SHALL GRIAULE BE LIABLE FOR ANY DIRECT, INDIRECT,
'INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
'NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
'DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
'THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
'(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
'THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
'
'You can download the free version of GrFinger directly from Griaule website.
'
'These notices must be retained in any copies of any part of this
'documentation and/or sample.
'
'-------------------------------------------------------------------------------

' -----------------------------------------------------------------------------------
' Support and fingerprint management routines
' -----------------------------------------------------------------------------------

Option Explicit
Option Base 0

' Raw image data type.
Public Type RawImage
    ' Image data.
    img() As Byte
    ' Image width.
    width As Long
    ' Image height.
    height As Long
    ' Image resolution.
    res As Long
End Type

' Template data Type
Public Type TTemplate
    ' Template data
    tpt() As Byte
    ' Template size
    Size As Long
End Type

' A bitmap picture structure (auxiliary type)
Private Type PicBmp
    Size As Long
    Type As Long
    hBmp As Long
    hPal As Long
    Reserved As Long
End Type

' A GUI structure (auxiliary type)
Private Type GUID
    Data1 As Long
    Data2 As Integer
    Data3 As Integer
    Data4(7) As Byte
End Type

' Some constants to make our code cleaner
Public Const ERR_CANT_OPEN_BD = -999
Public Const ERR_INVALID_ID = -998
Public Const ERR_INVALID_TEMPLATE = -997

' Importing necessary functions
' This function counts the length of a string pointed by pString.
Public Declare Function lstrlen Lib "kernel32" Alias "lstrlenA" (ByVal pString As Long) As Long
' This function copies [lByteCount] bytes from [pSource] memory position th [pDestination].
Public Declare Sub CopyMemory Lib "kernel32" Alias "RtlMoveMemory" (ByRef pDestination As Any, ByRef pSource As Any, ByVal lByteCount As Long)
' This function creates an IPictureDisp from a bitmap
Private Declare Function OleCreatePictureIndirect Lib "olepro32.dll" (PicDesc As PicBmp, RefIID As GUID, ByVal fPictureOwnsHandle As Long, IPic As IPicture) As Long

' The last acquired image.
Public raw As RawImage
' The template extracted from last acquired image.
Public template As TTemplate
' Database class.
Public DB As DBClass

' -----------------------------------------------------------------------------------
' Support functions
' -----------------------------------------------------------------------------------

' Create an IPictureDisp object from a Bitmap Handle.
Public Function PictureFromHandle(ByVal hBmp As Long) As IPictureDisp
    Dim r As Long
    Dim pic As PicBmp
    ' IPicture requires a reference to "Standard OLE Types"
    Dim IPic As IPictureDisp
    Dim IID_IDispatch As GUID
    ' Fill in with IDispatch Interface ID
    With IID_IDispatch
        .Data1 = &H7BF80980
        .Data2 = &HBF32
        .Data3 = &H101A
        .Data4(0) = &H8B
        .Data4(1) = &HBB
        .Data4(2) = &H0
        .Data4(3) = &HAA
        .Data4(4) = &H0
        .Data4(5) = &H30
        .Data4(6) = &HC
        .Data4(7) = &HAB
    End With
    ' Fill Pic with necessary parts
    With pic
        .Size = Len(pic)          ' Length of structure
        .Type = vbPicTypeBitmap   ' Type of Picture (bitmap)
        .hBmp = hBmp              ' Handle to bitmap
    End With
    ' Create Picture object
    r = OleCreatePictureIndirect(pic, IID_IDispatch, 1, IPic)
    ' Return new Picture object
    Set PictureFromHandle = IPic
End Function

' Creates a String from a pointer. Used to convert the DLL string to a VB String.
Public Function ptrToStr(ByVal ptr As Long) As String
    Dim count As Long
    Dim str As String
    ' Getting string length
    count = lstrlen(ptr)
    If count Then
        ' Creating destination string.
        str = String$(count, vbNullChar)
        ' Copying from memory to new String.
        CopyMemory ByVal str, ByVal ptr, count
        ptrToStr = str
    Else
        ptrToStr = ""
    End If
End Function

' Creates a Byte array of size [Size] and copies memory content pointed by [ptr] to it.
' It is used to copy byte array returned by the DLL to the VB style byte array.
Public Function ptrToByteArr(ByVal ptr As Long, Size As Long) As Byte()
    ' Creating array.
    Dim arr() As Byte
    ' Setting array size.
    ReDim arr(Size)
        
    ' Copying memory content to array.
    CopyMemory arr(0), ByVal ptr, Size
    ptrToByteArr = arr
End Function

' Write a message to log
Public Sub writeLog(msg As String)
    ' add message
    formMain.lbLog.AddItem (msg)
    formMain.lbLog.ListIndex = formMain.lbLog.ListCount - 1
    formMain.lbLog.ListIndex = -1
End Sub

' Change Event codes into friendly messagens
Public Sub WriteEvent(idSensor As String, eventCode As Long)
    Select Case eventCode
        Case GR_PLUG
            writeLog ("Sensor: " & idSensor & ". Event: Plugged.")
        Case GR_UNPLUG
            writeLog ("Sensor: " & idSensor & ". Event: Unplugged.")
        Case GR_FINGER_DOWN
            writeLog ("Sensor: " & idSensor & ". Event: Finger Placed.")
        Case GR_FINGER_UP
            writeLog ("Sensor: " & idSensor & ". Event: Finger Removed.")
        Case GR_IMAGE
            writeLog ("Sensor: " & idSensor & ". Event: Image Captured.")
        Case Else
            writeLog ("Sensor: " & idSensor & ". Event:(" & eventCode & ")")
    End Select
End Sub

' Write and describe an error.
Public Sub writeError(errorCode As Long)
    Select Case errorCode
        Case GR_ERROR_INITIALIZE_FAIL
            writeLog ("Fail to Initialize GrFingerX. (Error:" & errorCode & ")")
        Case GR_ERROR_NOT_INITIALIZED
            writeLog ("The GrFingerX Library is not initialized. (Error:" & errorCode & ")")
        Case GR_ERROR_FAIL_LICENSE_READ
            writeLog ("License not found. See manual for troubleshooting. (Error:" & errorCode & ")")
            MsgBox ("License not found. See manual for troubleshooting.")
        Case GR_ERROR_NO_VALID_LICENSE
            writeLog ("The license is not valid. See manual for troubleshooting. (Error:" & errorCode & ")")
            MsgBox ("The license is not valid. See manual for troubleshooting.")
        Case GR_ERROR_NULL_ARGUMENT
            writeLog ("The parameter have a null value. (Error:" & errorCode & ")")
        Case GR_ERROR_FAIL
            writeLog ("Fail to create a GDI object. (Error:" & errorCode & ")")
        Case GR_ERROR_ALLOC
            writeLog ("Fail to create a context. Cannot allocate memory. (Error:" & errorCode & ")")
        Case GR_ERROR_PARAMETERS
            writeLog ("One or more parameters are out of bound. (Error:" & errorCode & ")")
        Case GR_ERROR_WRONG_USE
            writeLog ("This function cannot be called at this time. (Error:" & errorCode & ")")
        Case GR_ERROR_EXTRACT
            writeLog ("Template Extraction failed. (Error:" & errorCode & ")")
        Case GR_ERROR_SIZE_OFF_RANGE
            writeLog ("Image is too larger or too short.  (Error:" & errorCode & ")")
        Case GR_ERROR_RES_OFF_RANGE
            writeLog ("Image have too low or too high resolution. (Error:" & errorCode & ")")
        Case GR_ERROR_CONTEXT_NOT_CREATED
            writeLog ("The Context could not be created. (Error:" & errorCode & ")")
        Case GR_ERROR_INVALID_CONTEXT
            writeLog ("The Context does not exist. (Error:" & errorCode & ")")

            ' Capture error codes

        Case GR_ERROR_CONNECT_SENSOR
            writeLog ("Error while connection to sensor. (Error:" & errorCode & ")")
        Case GR_ERROR_CAPTURING
            writeLog ("Error while capturing from sensor. (Error:" & errorCode & ")")
        Case GR_ERROR_CANCEL_CAPTURING
            writeLog ("Error while stop capturing from sensor. (Error:" & errorCode & ")")
        Case GR_ERROR_INVALID_ID_SENSOR
            writeLog ("The idSensor is invalid. (Error:" & errorCode & ")")
        Case GR_ERROR_SENSOR_NOT_CAPTURING
            writeLog ("The sensor is not capturing. (Error:" & errorCode & ")")
        Case GR_ERROR_INVALID_EXT
            writeLog ("The File have a unknown extension. (Error:" & errorCode & ")")
        Case GR_ERROR_INVALID_FILENAME
            writeLog ("The filename is invalid. (Error:" & errorCode & ")")
        Case GR_ERROR_INVALID_FILETYPE
            writeLog ("The file type is invalid. (Error:" & errorCode & ")")
        Case GR_ERROR_SENSOR
            writeLog ("The sensor raise an error. (Error:" & errorCode & ")")

            ' Our error codes

        Case ERR_INVALID_TEMPLATE
            writeLog ("Invalid Template. (Error:" & errorCode & ")")
        Case ERR_INVALID_ID
            writeLog ("Invalid ID. (Error:" & errorCode & ")")
        Case ERR_CANT_OPEN_BD
            writeLog ("Unable to connect to DataBase. (Error:" & errorCode & ")")
        Case Else
            writeLog ("Error:" & errorCode)
    End Select
End Sub

' Check if we have a valid template
Public Function TemplateIsValid() As Boolean
    ' Check template size
    TemplateIsValid = (template.Size > 0)
End Function

' -----------------------------------------------------------------------------------
' Main functions for fingerprint recognition management
' -----------------------------------------------------------------------------------

' Initializes GrFinger DLL and all necessary utilities.
Public Function InitializeGrFinger() As Long
    Dim err As Integer
    
    ' Opening database
    Set DB = New DBClass
    If DB.OpenDB() = False Then
        InitializeGrFinger = ERR_CANT_OPEN_BD
        Exit Function
    End If
    ' Create a new Template
    ReDim template.tpt(GR_MAX_SIZE_TEMPLATE) As Byte
    ' Create a new raw image
    raw.width = 0
    raw.height = 0
    ' Initializing library
    err = GrInitialize
    If err < 0 Then
        InitializeGrFinger = err
        Exit Function
    End If
    InitializeGrFinger = InitializeGrCap
End Function

' Finalize library and close DB.
Public Sub FinalizeGrFinger()
    ' finalize library
    GrCapFinalize
    GrFinalize
    ' Closing database
    DB.closeDB
    Set DB = Nothing
End Sub

' Display fingerprint image on screen
Public Sub PrintBiometricDisplay(ByVal biometricDisplay As Boolean, ByVal context As Integer)
    ' handle to finger image
    Dim handle As Long
    ' return value
    Dim ret As Integer

    If biometricDisplay Then
        ' get image with biometric info
        GrBiometricDisplay template.tpt(0), raw.img(0), raw.width, raw.height, raw.res, formMain.hdc, handle, context
    Else
        ' get raw image
        GrCapRawImageToHandle raw.img(0), raw.width, raw.height, formMain.hdc, handle
    End If

    ' draw image on picture box
    If Not (handle = 0) Then
        formMain.img.Picture = PictureFromHandle(handle)
    End If
End Sub

' Add a fingerprint template to database
Public Function Enroll() As Integer
    ' Checking if template is valid.
    If TemplateIsValid() Then
        ' Adds template to database and gets ID.
        Enroll = DB.AddTemplate(template.tpt)
        Exit Function
    End If
    Enroll = -1
End Function

' Extract a fingerprint template from current image
Public Function ExtractTemplate() As Integer
    Dim ret As Integer
    
    ' set current buffer size for extract template
    template.Size = GR_MAX_SIZE_TEMPLATE
    ' reallocate template buffer
    ReDim Preserve template.tpt(template.Size)
    ret = GrExtract(raw.img(0), raw.width, raw.height, raw.res, template.tpt(0), template.Size, GR_DEFAULT_CONTEXT)
    ' if error, set template size to 0
    ' Result < 0 => extraction problem
    If ret < 0 Then template.Size = 0
    ' Set real buffer size and free unecessary data
    ReDim Preserve template.tpt(template.Size)
    ExtractTemplate = ret
End Function

' Identify current fingerprint on our database
Public Function Identify(ByRef score As Long) As Integer
    Dim ret As Integer
    Dim i As Integer
    
    ' Starting identification process and supplying query template.
    If Not TemplateIsValid() Then
        Identify = ERR_INVALID_TEMPLATE
        Exit Function
    End If
    
    ' Starting identification process and supplying query template.
    ret = GrIdentifyPrepare(template.tpt(0), GR_DEFAULT_CONTEXT)
    ' error?
    If ret < 0 Then
        Identify = ret
        Exit Function
    End If
    
    ' Getting enrolled templates from database.
    Dim rs As ADODB.Recordset
    Set rs = DB.getTemplates
    Dim tpt() As Byte
    ' Iterate over all templates in database
    Do Until rs.EOF
        ' Getting current template from recordset.
        tpt = rs("template")
        ' Comparing current template.
        ret = GrIdentify(tpt(0), score, GR_DEFAULT_CONTEXT)
        ' Checking if query template and reference template match.
        If ret = GR_MATCH Then
            Identify = rs("ID")
            rs.Close
            Exit Function
        ElseIf ret < 0 Then
            Identify = ret
            Exit Function
        End If
        rs.MoveNext
    Loop
    ' Closing recordset.
    rs.Close
    ' end of database, return "no match" code
    Identify = GR_NOT_MATCH
End Function

' Performs an verification using the last extracted template.
Public Function Verify(ByVal id As Long, ByRef score As Long) As Integer
    Dim tpt() As Byte
    
    ' Checking if template is valid.
    If Not TemplateIsValid() Then
        Verify = ERR_INVALID_TEMPLATE
        Exit Function
    End If
    ' Getting template with supplied ID from database.
    tpt = DB.getTemplate(id)
    ' Checking if ID was found.
    If UBound(tpt) = 0 Then
        Verify = ERR_INVALID_ID
        Exit Function
    End If
    ' Comparing templates.
    Verify = GrVerify(template.tpt(0), tpt(0), score, GR_DEFAULT_CONTEXT)
End Function

' Show GrFinger version and type
Public Sub MessageVersion()
    Dim majorVersion As Byte
    Dim minorVersion As Byte
    Dim ret As Long
    Dim vStr As String
    
    majorVersion = 0
    minorVersion = 0
    vStr = ""

    ret = GrGetGrFingerVersion(majorVersion, minorVersion)
    If ret = GRFINGER_FULL Then vStr = "FULL"
    If ret = GRFINGER_LIGHT Then vStr = "LIGHT"
    If ret = GRFINGER_FREE Then vStr = "FREE"
    Call MsgBox("The GrFinger DLL version is " & majorVersion & _
            "." & minorVersion & "." & vbCrLf & _
            "The license type is '" & vStr & "'.", , "GrFinger Version")
End Sub

